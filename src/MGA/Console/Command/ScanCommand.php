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
        'Server'       => 'Web server',
        'X-Powered-By' => 'Software'
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
     * @param  InputInterface   $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->url = $this->cleanUrl($input->getArgument('url'));
        $response = $this->makeRequest($this->url, array(
            CURLOPT_NOBODY => true
        ));
        if (isset($response['header']['Location'])) {
            $this->url = $response['header']['Location'];
        }
        $this->input  = $input;
        $this->output = $output;
        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);
        $this->output->writeln('Scanning <info>' . $this->url . '</info>...');

        $this->sitemapExists();
        $this->serverTech();
        $this->unreachablePath();
    }

    /**
     * Check HTTP status codes for files/paths that shouldn't be reachable
     */
    protected function unreachablePath()
    {
        $this->writeHeader('Unreachable Path Check');
        $rows = array();
        foreach ($this->unreachablePath as $path) {
            $response = $this->makeRequest($this->url . $path, array(
                CURLOPT_NOBODY => true,
                CURLOPT_FOLLOWLOCATION => true
            ));
            $rows[] = array(
                $path,
                $response['code'],
                $response['code'] == 200
                    ? '<error>Fail</error>'
                    : '<bg=green>Pass</bg=green>'
            );
        }
        $this->getHelper('table')
            ->setHeaders(array('Path', 'Response Code', 'Status'))
            ->setRows($rows)
            ->render($this->output);
    }

    /**
     * Analize the server technology being used
     */
    protected function serverTech()
    {
        $this->writeHeader('Server Technology');
        $response = $this->makeRequest($this->url, array(
            CURLOPT_NOBODY => true
        ));
        $rows = array();
        foreach ($this->techHeader as $key => $value) {
            $rows[] = array(
                $value,
                isset($response['header'][$key])
                    ? $response['header'][$key]
                    : 'Unknown'
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
    protected function sitemapExists()
    {
        $this->writeHeader('Sitemap');
        $file = $this->getSitemapFile();
        $response = $this->makeRequest($this->url . $file, array(
            CURLOPT_NOBODY => true
        ));
        if ($response['code'] == 200) {
            $this->output->writeln('<info>Sitemap is accessible:</info> ' . $this->url . $file);
        } else {
            $this->output->writeln('<error>Sitemap is not accessible:</error> ' . $this->url . $file);
        }
    }

    /**
     * Parse the robots.txt text file to find the sitemap
     * 
     * @return string
     */
    protected function getSitemapFile()
    {
        $response = $this->makeRequest($this->url . 'robots.txt');
        $found = preg_match('/Sitemap: (.*)/mi', $response['body'], $match);
        if ($response['code'] != 200 || !$found || !isset($match[1])) {
            $this->output->writeln('<error>Sitemap is not declared in robots.txt</error>');
            return 'sitemap.xml';
        } else {
            $this->output->writeln('<info>Sitemap is declared in robots.txt</info>');
            return trim(str_replace($this->url, '', $match[1]));
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
