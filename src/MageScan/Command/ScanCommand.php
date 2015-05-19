<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Command;

use MageScan\Check\Catalog;
use MageScan\Check\Module;
use MageScan\Check\Patch;
use MageScan\Check\Sitemap;
use MageScan\Check\TechHeader;
use MageScan\Check\UnreachablePath;
use MageScan\Check\Version;
use MageScan\Request;
use MageScan\Url;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Add scan command and run tests
 */
class ScanCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * URL of Magento site
     *
     * @var string
     */
    private $url;

    /**
     * Configure scan command
     */
    protected function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('Audit a Magento site as best you can by URL')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The URL of the Magento application'
            )
            ->addOption(
                'all-paths',
                null,
                InputOption::VALUE_NONE,
                'Crawl all urls that should not be reachable'
            )
            ->addOption(
                'show-modules',
                null,
                InputOption::VALUE_NONE,
                'Show all modules that were scanned for, not just matches'
            )
        ;
    }

    /**
     * Run scan command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input   = $input;
        $this->output  = $output;
        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);

        $this->setUrl($input->getArgument('url'));
        $this->output->writeln('Scanning <info>' . $this->url . '</info>...');

        $this->checkMagentoInfo();
        $this->checkModules($input->getOption('show-modules'));
        $this->checkCatalog();
        $this->checkPatch();
        $this->checkSitemapExists();
        $this->checkServerTech();
        $this->checkUnreachablePath($input->getOption('all-paths'));
    }

    /**
     * Get information about the Magento application
     */
    protected function checkMagentoInfo()
    {
        $this->writeHeader('Magento Information');
        $request = new Request;
        $response = $request->fetch(
            $this->url . 'js/varien/product.js',
            array(
                CURLOPT_FOLLOWLOCATION => true
            )
        );
        $version = new Version;
        $edition = $version->getMagentoEdition($response);
        $version = $version->getMagentoVersion($response, $edition);
        $rows = array(
            array('Edition', $edition),
            array('Version', $version)
        );
        $this->getHelper('table')
            ->setHeaders(array('Parameter', 'Value'))
            ->setRows($rows)
            ->render($this->output);
    }

    /**
     * Check for files known to be associated with a module
     *
     * @param boolean $all
     */
    protected function checkModules($all = false)
    {
        $this->writeHeader('Installed Modules');
        $module = new Module;
        $found = $notFound = array();
        foreach ($module->checkForModules($this->url) as $name => $exists) {
            if ($exists) {
                $found[] = array($name, '<bg=green>Yes</bg=green>');
            } else {
                $notFound[] = array($name, 'No');
            }
        }
        if (empty($found) && !$all) {
            $this->output->writeln('No detectable modules were found');
            return;
        }
        if ($all) {
            $found = array_merge($found, $notFound);
        }
        $this->getHelper('table')
            ->setHeaders(array('Module', 'Installed'))
            ->setRows($found)
            ->render($this->output);
    }

    /**
     * Get catalog data
     */
    protected function checkCatalog()
    {
        $this->writeHeader('Catalog Information');
        $rows     = array();
        $catalog  = new Catalog;
        $categoryCount = $catalog->categoryCount($this->url);
        $rows[] = array(
            'Categories',
            $categoryCount !== false ? $categoryCount : 'Unknown'
        );
        $productCount = $catalog->productCount($this->url);
        $rows[] = array(
            'Products',
            $productCount !== false ? $productCount : 'Unknown'
        );
        $this->getHelper('table')
            ->setHeaders(array('Type', 'Count'))
            ->setRows($rows)
            ->render($this->output);
    }

    /**
     * Check for installed patches
     */
    protected function checkPatch()
    {
        $this->writeHeader('Patches');
        $rows    = array();
        $patch   = new Patch;
        $patches = $patch->checkAll($this->url);
        foreach ($patches as $name => $result) {
            switch ($result) {
                case PATCH::PATCHED:
                    $status = '<bg=green>Patched</bg=green>';
                    break;
                case PATCH::UNPATCHED:
                    $status = '<error>Unpatched</error>';
                    break;
                default:
                    $status = 'Unknown';
            }
            $rows[] = array(
                $name,
                $status
            );
        }
        $this->getHelper('table')
            ->setHeaders(array('Name', 'Status'))
            ->setRows($rows)
            ->render($this->output);
    }

    /**
     * Check HTTP status codes for files/paths that shouldn't be reachable
     */
    protected function checkUnreachablePath($all = false)
    {
        $this->writeHeader('Unreachable Path Check');
        $unreachablePath = new UnreachablePath;
        $results = $unreachablePath->checkPaths($this->url, $all);
        foreach ($results as &$result) {
            if ($result[2] === false) {
                $result[2] = '<error>Fail</error>';
            } elseif ($result[2] === true) {
                $result[2] = '<bg=green>Pass</bg=green>';
            }
        }
        $this->getHelper('table')
            ->setHeaders(array('Path', 'Response Code', 'Status'))
            ->setRows($results)
            ->render($this->output);
    }

    /**
     * Analize the server technology being used
     */
    protected function checkServerTech()
    {
        $this->writeHeader('Server Technology');
        $techHeader = new TechHeader;
        $values = $techHeader->getHeaders($this->url);
        if (empty($values)) {
            $this->output->writeln('No detectable technology was found');
            return;
        }
        $rows = array();
        foreach ($values as $key => $value) {
            $rows[] = array($key, $value);
        }
        $this->getHelper('table')
            ->setHeaders(array('Key', 'Value'))
            ->setRows($rows)
            ->render($this->output);
    }

    /**
     * Check that the store is correctly using a sitemap
     */
    protected function checkSitemapExists()
    {
        $this->writeHeader('Sitemap');
        $url = $this->getSitemapUrl();
        $request = new Request;
        $response = $request->fetch($url, array(
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => true
        ));
        if ($response->code == 200) {
            $this->output
                ->writeln('<info>Sitemap is accessible:</info> ' . $url);
        } else {
            $this->output
                ->writeln('<error>Sitemap is not accessible:</error> ' . $url);
        }
    }

    /**
     * Parse the robots.txt text file to find the sitemap
     *
     * @return string
     */
    protected function getSitemapUrl()
    {
        $request = new Request;
        $response = $request->fetch($this->url . 'robots.txt');
        $sitemap = new Sitemap;
        $sitemap  = $sitemap->getSitemapFromRobotsTxt($response);
        if ($sitemap === false) {
            $this->output->writeln(
                '<error>Sitemap is not declared in robots.txt</error>'
            );
            return $this->url . 'sitemap.xml';
        }
        $this->output
            ->writeln('<info>Sitemap is declared in robots.txt</info>');
        return (string) $sitemap;
    }

    /**
     * Validate and set url
     *
     * @param  string                   $input
     * @throws InvalidArgumentException
     */
    protected function setUrl($input)
    {
        $url = new Url;
        $this->url = $url->clean($input);
        $request = new Request;
        $response = $request->fetch($this->url, array(
            CURLOPT_NOBODY => true
        ));
        if ($response->code == 0) {
            throw new \InvalidArgumentException(
                'Could not connect to URL: ' . $this->url
            );
        }
        if (isset($response->header['Location'])) {
            $this->url = $response->header['Location'];
        }
    }

    /**
     * Write a header block
     *
     * @param  string $text
     * @param  string $style
     */
    protected function writeHeader($text, $style = 'bg=blue;fg=white')
    {
        $this->output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')
                ->formatBlock($text, $style, true),
            '',
        ));
    }
}
