<?php

namespace MGA\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @param  Symfony\Component\Console\Input\InputInterface   $input
     * @param  Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->url    = $this->cleanUrl($input->getArgument('url'));
        $this->input  = $input;
        $this->output = $output;
        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);
        $this->output->writeln('Scanning <info>' . $this->url . '</info>...');
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
            $response = $this->makeRequest($this->url . $path, true);
            $rows[]   = array(
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
        $response = $this->makeRequest($this->url, true);
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
     * Create a curl request for a given url
     * 
     * @param  string  $url
     * @param  boolean $noBody
     * @return array
     */
    protected function makeRequest($url, $noBody = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, $noBody);
        $response   = curl_exec($ch);
        $code       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $header = substr($response, 0, $headerSize);
        $body   = substr($response, $headerSize);
        if ($code == 0) {
            throw new \Exception('Couldn\'t connect to URL');
        }
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
     * @param  string $url
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
