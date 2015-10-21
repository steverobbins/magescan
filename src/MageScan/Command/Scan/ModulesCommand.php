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

use MageScan\Check\Module;
use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get information about installed extensions.
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class ModulesCommand extends ScanCommand
{
    /**
     * Configure scan command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:modules')
            ->setDescription('Attempt to detect installed modules.')
            ->addOption(
                'show-modules',
                null,
                InputOption::VALUE_NONE,
                'Show all modules that were scanned for, not just matches'
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
        if (empty($found) && !$input->getOption('show-modules')) {
            $this->output->writeln('No detectable modules were found');
            return;
        }
        if ($input->getOption('show-modules')) {
            $found = array_merge($found, $notFound);
        }
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Module', 'Installed'))
            ->setRows($found)
            ->render();
    }
}