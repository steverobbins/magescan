<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan a Magento site using PHP CLI
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class ScanCommand extends Command
{
    /**
     * Input object
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * Output object
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * URL of Magento site
     *
     * @var string
     */
    protected $url;

    /**
     * Cached request object with desired secure flag
     *
     * @var \MageScan\Request
     */
    protected $request;

    /**
     * Configure scan command
     *
     * @return void
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
                'show-modules',
                null,
                InputOption::VALUE_NONE,
                'Show all modules that were scanned for, not just matches'
            )
            ->addOption(
                'insecure',
                'k',
                InputOption::VALUE_NONE,
                'Don\'t validate SSL certificate if URL is https'
            );
    }

    /**
     * Run scan command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input   = $input;
        $this->output  = $output;
        $this->request = new Request;
        $this->request->setInsecure($this->input->getOption('insecure'));

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
        $this->checkUnreachablePath();
    }

    /**
     * Get information about the Magento application
     *
     * @return void
     */
    protected function checkMagentoInfo()
    {
        $this->writeHeader('Magento Information');
        $version = new Version;
        $version->setRequest($this->request);
        $version = $version->getInfo($this->url);
        $rows = array(
            array('Edition', $version[0] ?: 'Unknown'),
            array('Version', $version[1] ?: 'Unknown')
        );
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Parameter', 'Value'))
            ->setRows($rows)
            ->render();
    }

    /**
     * Check for files known to be associated with a module
     *
     * @param boolean $all
     *
     * @return void
     */
    protected function checkModules($all = false)
    {
        $this->writeHeader('Installed Modules');
        $module = new Module;
        $module->setRequest($this->request);
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
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Module', 'Installed'))
            ->setRows($found)
            ->render();
    }

    /**
     * Get catalog data
     *
     * @return void
     */
    protected function checkCatalog()
    {
        $this->writeHeader('Catalog Information');
        $rows     = array();
        $catalog  = new Catalog;
        $catalog->setRequest($this->request);
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
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Type', 'Count'))
            ->setRows($rows)
            ->render();
    }

    /**
     * Check for installed patches
     *
     * @return void
     */
    protected function checkPatch()
    {
        $this->writeHeader('Patches');
        $rows    = array();
        $patch   = new Patch;
        $patch->setRequest($this->request);
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
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Name', 'Status'))
            ->setRows($rows)
            ->render();
    }

    /**
     * Check HTTP status codes for files/paths that shouldn't be reachable
     *
     * @return void
     */
    protected function checkUnreachablePath()
    {
        $this->writeHeader('Unreachable Path Check');
        $unreachablePath = new UnreachablePath;
        $unreachablePath->setRequest($this->request);
        $results = $unreachablePath->checkPaths($this->url);
        foreach ($results as &$result) {
            if ($result[2] === false) {
                $result[2] = '<error>Fail</error>';
            } elseif ($result[2] === true) {
                $result[2] = '<bg=green>Pass</bg=green>';
            }
        }
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Path', 'Response Code', 'Status'))
            ->setRows($results)
            ->render();
    }

    /**
     * Analize the server technology being used
     *
     * @return void
     */
    protected function checkServerTech()
    {
        $this->writeHeader('Server Technology');
        $techHeader = new TechHeader;
        $techHeader->setRequest($this->request);
        $values = $techHeader->getHeaders($this->url);
        if (empty($values)) {
            $this->output->writeln('No detectable technology was found');
            return;
        }
        $rows = array();
        foreach ($values as $key => $value) {
            $rows[] = array($key, $value);
        }
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Key', 'Value'))
            ->setRows($rows)
            ->render();
    }

    /**
     * Check that the store is correctly using a sitemap
     *
     * @return void
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
        $sitemap->setRequest($this->request);
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
     * @param string $input
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setUrl($input)
    {
        $url = new Url;
        $this->url = $url->clean($input);
        $request = $this->request;
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
     * @param string $text
     * @param string $style
     *
     * @return void
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
