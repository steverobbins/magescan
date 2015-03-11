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
    private $url;
    private $input;
    private $output;

    protected $unreachablePath = array(
        'admin',
        'app/etc/local.xml',
        'var/log/system.log',
        'var/log/exception.log'
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
        $this->url    = $this->cleanUrl($input->getArgument('url'));
        $this->input  = $input;
        $this->output = $output;
        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);
        $this->output->writeln('Scanning <info>' . $this->url . '</info>...');
        $this->checkUnreachablePath();
    }

    /**
     * Check HTTP status codes for files/paths that shouldn't be reachable
     */
    protected function checkUnreachablePath()
    {
        $this->writeHeader('Unreachable Path Check');
        $rows = array();
        foreach ($this->unreachablePath as $path) {
            $response = $this->makeRequest($this->url . $path);
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
     * Create a curl request for a given url
     * 
     * @param  string $url
     * @return array
     */
    protected function makeRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 0) {
            throw new \Exception('Couldn\'t resolve given URL');
        }
        return array(
            'code' => $code,
            'body' => $response
        );
    }

    /**
     * Get the full, valid url from input
     * This could probably written better
     * 
     * @param  string $url
     * @return string
     */
    protected function cleanUrl($url)
    {
        if (substr($url, -1) != '/') {
            $url .= '/';
        }
        return $url;
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
