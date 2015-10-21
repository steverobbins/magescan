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

use MageScan\Check\Patch;
use MageScan\Check\Version;
use MageScan\Request;
use MageScan\Url;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for scan commands with basic bootstrapping.
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
abstract class ScanCommand extends Command
{
    /**
     * Configure scan command
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
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input   = $input;
        $this->output  = $output;

        $style = new OutputFormatterStyle('white', 'blue', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);

        $this->request = new Request;
        $this->request->setInsecure($input->getOption('insecure'));

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