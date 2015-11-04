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

use MageScan\Url;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run all scan commands
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class AllCommand extends AbstractCommand
{
    /**
     * Names of all the scans
     *
     * @var array
     */
    protected $scanNames = [
        'scan:version',
        'scan:module',
        'scan:catalog',
        'scan:patch',
        'scan:sitemap',
        'scan:server',
        'scan:unreachable',
    ];

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:all')
            ->setDescription('Run all scans')
            ->addOption(
                'show-modules',
                null,
                InputOption::VALUE_NONE,
                'Show all modules that were scanned for, not just matches'
            );
        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = new Url;
        $url = $url->clean($input->getArgument('url'));
        $format = $input->getOption('format');
        $this->executeStart($format, $url);
        $scanCount = count($this->scanNames);
        foreach ($this->scanNames as $i => $commandName) {
            $command = $this->getApplication()->find($commandName);
            $args = [
                'command'  => $commandName,
                'url'      => $url,
                '--format' => $format,
            ];
            if ($commandName === 'scan:module' && $input->getOption('show-modules')) {
                $args['--show-modules'] = true;
            }
            $command->run(new ArrayInput($args), $output);
            if (++$i < $scanCount) {
                $this->afterScan($format);
            }
        }
        $this->executeEnd($format);
    }

    /**
     * Things to output when execusion starts
     *
     * @param string $format
     * @param string $url
     *
     * @return void
     */
    protected function executeStart($format, $url)
    {
        switch ($format) {
            case 'json':
                echo '[';
                break;
            default:
                $this->output->writeln(sprintf('Scanning <info>%s</info>...', $url));
        }
    }

    /**
     * Things to output when execusion ends
     *
     * @param string $format
     *
     * @return void
     */
    protected function executeEnd($format)
    {
        switch ($format) {
            case 'json':
                echo ']';
                break;
        }
    }

    /**
     * Things to output after a scan
     *
     * @param string $format
     *
     * @return void
     */
    protected function afterScan($format)
    {
        switch ($format) {
            case 'json':
                echo ',';
                break;
        }
    }
}
