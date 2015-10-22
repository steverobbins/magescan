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
        foreach ([
            'scan:version',
            'scan:module',
            'scan:catalog',
            'scan:patch',
            'scan:sitemap',
            'scan:server',
            'scan:unreachable',
        ] as $commandName) {
            $command = $this->getApplication()->find($commandName);
            $args = [
                'command' => $commandName,
                'url' => $input->getArgument('url')
            ];
            if ($commandName === 'scan:module' && $input->getOption('show-modules')) {
                $args['--show-modules'] = true;
            }
            $command->run(new ArrayInput($args), $output);
        }
    }
}
