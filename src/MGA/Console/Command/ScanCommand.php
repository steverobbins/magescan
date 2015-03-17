<?php
/**
 * Magento Guest Audit
 *
 * PHP version 5
 * 
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magento-guest-audit
 */

namespace MGA\Console\Command;

use MGA\Magento\Version;
use MGA\Request;
use MGA\Sitemap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * @var string
     */
    private $url;

    /**
     * List of paths that we shouldn't be able to access
     * @var array
     */
    protected $unreachablePath = array(
        'admin',
        'app/etc/local.xml',
        'phpinfo.php',
        'var/export/export_all_products.csv',
        'var/export/export_product_stocks.csv',
        'var/export/export_customers.csv',
        'var/log/exception.log',
        'var/log/payment_authnetcim.log',
        'var/log/payment_authorizenet.log',
        'var/log/payment_authorizenet_directpost.log',
        'var/log/payment_cybersource_soap.log',
        'var/log/payment_ogone.log',
        'var/log/payment_payflow_advanced.log',
        'var/log/payment_payflow_link.log',
        'var/log/payment_paypal_billing_agreement.log',
        'var/log/payment_paypal_direct.log',
        'var/log/payment_paypal_express.log',
        'var/log/payment_paypal_standard.log',
        'var/log/payment_paypaluk_express.log',
        'var/log/payment_pbridge.log',
        'var/log/payment_verisign.log',
        'var/log/system.log',
    );

    /**
     * Headers that provide information about the technology used
     * @var array
     */
    protected $techHeader = array(
        'Server',
        'Via',
        'X-Mod-Pagespeed',
        'X-Powered-By',
    );

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
        $this->checkSitemapExists();
        $this->checkServerTech();
        $this->checkUnreachablePath();
    }

    /**
     * Get information about the Magento application
     */
    protected function checkMagentoInfo()
    {
        $this->writeHeader('Magento Information');
        $response = Request::fetch(
            $this->url . 'js/varien/product.js', 
            array(
                CURLOPT_FOLLOWLOCATION => true
            )
        );
        $edition = Version::getMagentoEdition($response);
        $version = Version::getMagentoVersion($response, $edition);
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
     * Check HTTP status codes for files/paths that shouldn't be reachable
     */
    protected function checkUnreachablePath()
    {
        $this->writeHeader('Unreachable Path Check');
        $rows = array();
        foreach ($this->unreachablePath as $path) {
            $response = Request::fetch($this->url . $path, array(
                CURLOPT_NOBODY => true
            ));
            $rows[] = array(
                $path,
                $response->code,
                $this->getUnreachableStatus($response)
            );
        }
        $this->getHelper('table')
            ->setHeaders(array('Path', 'Response Code', 'Status'))
            ->setRows($rows)
            ->render($this->output);
    }

    /**
     * Get the status string for the given response
     * 
     * @param  \stdClass $response
     * @return string
     */
    protected function getUnreachableStatus(\stdClass $response)
    {
        switch ($response->code) {
            case 200:
                return '<error>Fail</error>';
            case 301:
            case 302:
                $redirect = $response->header['Location'];
                if ($redirect != $this->url) {
                    return $redirect;
                }
        }
        return '<bg=green>Pass</bg=green>';
    }

    /**
     * Analize the server technology being used
     */
    protected function checkServerTech()
    {
        $this->writeHeader('Server Technology');
        $response = Request::fetch($this->url, array(
            CURLOPT_NOBODY => true
        ));
        $rows = array();
        foreach ($this->techHeader as $value) {
            $rows[] = array(
                $value,
                isset($response->header[$value])
                    ? $response->header[$value]
                    : ''
            );
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
        $response = Request::fetch($url, array(
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
        $response = Request::fetch($this->url . 'robots.txt');
        $sitemap  = Sitemap::getSitemapFromRobotsTxt($response);
        if ($sitemap === false) {
            $this->output->writeln(
                '<error>Sitemap is not declared in robots.txt</error>'
            );
            return $this->url . 'sitemap.xml';
        }
        $this->output
            ->writeln('<info>Sitemap is declared in robots.txt</info>');
        return $sitemap;
    }

    /**
     * Validate and set url
     * 
     * @param  string                   $input
     * @throws InvalidArgumentException
     */
    protected function setUrl($input)
    {   
        $this->url = $this->cleanUrl($input);
        $response = Request::fetch($this->url, array(
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
     * Get the full, valid url from input
     * This could probably written better
     * 
     * @param  string $input
     * @return string
     */
    public function cleanUrl($input)
    {
        $bits = explode('://', $input);
        if (count($bits) > 1) {
            $protocol = $bits[0];
            unset($bits[0]);
        } else {
            $protocol = 'http';
        }
        $url  = implode($bits);
        $bits = explode('?', $url);
        if (substr($bits[0], -1) != '/') {
            $bits[0] .= '/';
        }
        return $protocol . '://' . implode('?', $bits);
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
