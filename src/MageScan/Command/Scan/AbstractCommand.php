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

namespace MageScan\Command\Scan;

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
 * Abstract scan command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
abstract class AbstractCommand extends Command
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
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The URL of the Magento application'
            )
            ->addOption(
                'insecure',
                'k',
                InputOption::VALUE_NONE,
                'Don\'t validate SSL certificate if URL is https'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify output format (default, json)',
                'default'
            );
    }

    /**
     * Initialize command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input   = $input;
        $this->output  = $output;
        $this->request = new Request;
        $this->request->setInsecure($this->input->getOption('insecure'));

        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);

        $this->setUrl($input->getArgument('url'));
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
        if (trim($input) == '') {
            throw new \InvalidArgumentException(
                'Target URL not specified'
            );
        }
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
     * Output information in the correct format
     *
     * @param string       $title
     * @param array|string $messages
     *
     * @return void
     */
    protected function out($title, $messages = [])
    {
        $format = $this->input->getOption('format');
        $method = 'outputFormat' . ucfirst($format);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(
                'Format "' . $format . '" is not supported'
            );
        }
        $this->$method($title, $messages);
    }

    /**
     * Output in default format
     *
     * @param string       $title
     * @param array|string $messages
     *
     * @return void
     */
    protected function outputFormatDefault($title, $messages)
    {
        $this->writeHeader($title);
        if (!is_array($messages)) {
            return $this->output->writeln($messages);
        }
        foreach ($messages as $message) {
            switch (isset($message['type']) ? $message['type'] : false) {
                case 'table':
                    $tableHelper = new Table($this->output);
                    $tableHelper
                        ->setHeaders($message['data'][0])
                        ->setRows($message['data'][1])
                        ->render();
                    break;
                default:
                    $this->output->writeln(is_array($message) ? $message['data'] : $message);
            }
        }
    }

    /**
     * Output in json format
     *
     * @param string       $title
     * @param array|string $messages
     *
     * @return void
     */
    protected function outputFormatJson($title, $messages)
    {
        $json = [
            'name'     => $title,
            'results'  => [],
            'messages' => [],
        ];
        if (!is_array($messages)) {
            $json['messages'][] = strip_tags($messages);
        } else {
            foreach ($messages as $message) {
                switch (isset($message['type']) ? $message['type'] : false) {
                    case 'table':
                        $result = [];
                        $headers = $message['data'][0];
                        array_map('strtolower', $headers);
                        foreach ($message['data'][1] as $row) {
                            foreach ($headers as $key => $name) {
                                $result[$name] = strip_tags($row[$key]);
                            }
                            $json['results'][] = $result;
                        }
                        break;
                    default:
                        $json['messages'][] = strip_tags(is_array($message) ? $message['data'] : $message);
                }
            }
        }
        echo json_encode($json);
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
