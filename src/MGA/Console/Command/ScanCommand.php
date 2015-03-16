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
    const EDITION_ENTERPRISE = 'Enterprise';
    const EDITION_COMMUNITY  = 'Community';

    /**
     * URL of Magento site
     * @var string
     */
    private $url;

    /**
     * @var Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

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
     * @param  InputInterface           $input
     * @param  OutputInterface          $output
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->url = $this->cleanUrl($input->getArgument('url'));
        $response = $this->makeRequest($this->url, array(
            CURLOPT_NOBODY => true
        ));
        if ($response['code'] == 0) {
            throw new \InvalidArgumentException(
                'Could not connect to URL: ' . $this->url
            );
        }
        if (isset($response['header']['Location'])) {
            $this->url = $response['header']['Location'];
        }
        $this->input  = $input;
        $this->output = $output;
        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);
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
        $response = $this->makeRequest(
            $this->url . 'js/varien/product.js', 
            array(
                CURLOPT_FOLLOWLOCATION => true
            )
        );
        $edition = $this->getMagentoEdition($response);
        $version = $this->getMagentoVersion($response, $edition);
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
            $response = $this->makeRequest($this->url . $path, array(
                CURLOPT_NOBODY => true
            ));
            $rows[] = array(
                $path,
                $response['code'],
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
     * @param  array  $response
     * @return string
     */
    protected function getUnreachableStatus(array $response)
    {
        switch ($response['code']) {
            case 200:
                return '<error>Fail</error>';
            case 301:
            case 302:
                $redirect = $response['header']['Location'];
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
        $response = $this->makeRequest($this->url, array(
            CURLOPT_NOBODY => true
        ));
        $rows = array();
        foreach ($this->techHeader as $value) {
            $rows[] = array(
                $value,
                isset($response['header'][$value])
                    ? $response['header'][$value]
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
        $response = $this->makeRequest($url, array(
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => true
        ));
        if ($response['code'] == 200) {
            $this->output
                ->writeln('<info>Sitemap is accessible:</info> ' . $url);
        } else {
            $this->output
                ->writeln('<error>Sitemap is not accessible:</error> ' . $url);
        }
    }

    /**
     * Guess Magento edition from license in public file
     *
     * @param  array  $response
     * @return string
     */
    protected function getMagentoEdition(array $response)
    {
        if ($response['code'] == 200) {
            preg_match('/@license.*/', $response['body'], $match);
            if (isset($match[0])) {
                return strpos($match[0], 'enterprise') !== false
                    ? self::EDITION_ENTERPRISE : self::EDITION_COMMUNITY;
            }
        }
        return 'Unknown';
    }

    /**
     * Guess Magento version from copyright in public file
     *
     * @param  array  $response
     * @param  string $edition
     * @return string
     */
    protected function getMagentoVersion(array $response, $edition)
    {
        if ($response['code'] == 200 && $edition != 'Unknown') {
            preg_match('/@copyright.*/', $response['body'], $match);
            if (
                isset($match[0])
                && preg_match('/[0-9-]{4,}/', $match[0], $match)
                && isset($match[0])
            ) {
                return $this->getMagentoVersionByYear($match[0], $edition);
            }
        }
        return 'Unknown';
    }

    /**
     * Guess Magento version from copyright year and edition
     * 
     * @param  string $year
     * @param  string $edition
     * @return string
     */
    protected function getMagentoVersionByYear($year, $edition)
    {
        switch ($year) {
            case '2006-2014':
                return $edition == self::EDITION_ENTERPRISE ? 
                    '1.14' : '1.9';
            case 2013:
                return $edition == self::EDITION_ENTERPRISE ? 
                    '1.13' : '1.8';
            case 2012:
                return $edition == self::EDITION_ENTERPRISE ? 
                    '1.12' : '1.7';
            case 2011:
                return $edition == self::EDITION_ENTERPRISE ? 
                    '1.11' : '1.6';
            case 2010:
                return $edition == self::EDITION_ENTERPRISE ? 
                    '1.9 - 1.10' : '1.4 - 1.5';
        }
    }

    /**
     * Parse the robots.txt text file to find the sitemap
     * 
     * @return string
     */
    protected function getSitemapUrl()
    {
        $response = $this->makeRequest($this->url . 'robots.txt');
        preg_match('/^(?!#+)\s*Sitemap: (.*)$/mi', $response['body'], $match);
        if ($response['code'] != 200 || !isset($match[1])) {
            $this->output->writeln(
                '<error>Sitemap is not declared in robots.txt</error>'
            );
            return $this->url . 'sitemap.xml';
        } else {
            $this->output
                ->writeln('<info>Sitemap is declared in robots.txt</info>');
            return trim($match[1]);
        }
    }

    /**
     * Create a curl request for a given url
     * 
     * @param  string $url
     * @param  boolean[]  $params
     * @return array
     */
    protected function makeRequest($url, array $params = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        foreach ($params as $key => $value) {
            curl_setopt($ch, $key, $value);
        }
        $response   = curl_exec($ch);
        $code       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $header = substr($response, 0, $headerSize);
        $body   = substr($response, $headerSize);
        return array(
            'code'   => $code,
            'header' => $this->parseHeader($header),
            'body'   => $body
        );
    }

    /**
     * Manipulate header data into a parsable format
     * 
     * @param  string $rawData
     * @return array
     */
    protected function parseHeader($rawData)
    {
        $data = array();
        foreach (explode("\r\n", $rawData) as $line) {
            $bits = explode(': ', $line);
            if (count($bits) == 2) {
                $data[$bits[0]] = $bits[1];
            }
        }
        return $data;
    }

    /**
     * Get the full, valid url from input
     * This could probably written better
     * 
     * @param  string $input
     * @return string
     */
    protected function cleanUrl($input)
    {
        $bits = explode('://', $input);
        if (count($bits) == 2) {
            $protocol = $bits[0];
            unset($bits[0]);
        } else {
            $protocol = 'http';
        }
        $url = implode($bits);
        if (substr($url, -1) != '/') {
            $url .= '/';
        }
        return $protocol . '://' . $url;
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
