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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan module command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class ModuleCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:modules')
            ->setDescription('Get installed modules')
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
        $all = $input->getOption('show-modules');
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
        if (empty($found) && !$all) {
            return $this->out('Installed Modules', 'No detectable modules were found');
        }
        if ($all) {
            $found = array_merge($found, $notFound);
        }
        $this->out('Installed Modules', [[
            'type' => 'table',
            'data' => [
                ['Module', 'Installed'],
                $found
            ]
        ]]);
    }
}
