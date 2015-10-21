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

use MageScan\Check\Patch;
use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get information about installed patches.
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class PatchesCommand extends ScanCommand
{
    /**
     * Configure scan command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:patches')
            ->setDescription('Attempt to detect which patches are installed.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeHeader('Patches');
        $rows    = array();
        $patch   = new Patch;
        $patch->setRequest($this->request);
        $patches = $patch->checkAll($this->url);
        foreach ($patches as $name => $result) {
            switch ($result) {
                case PATCH::PATCHED:
                    $status = '<bg=green>Patched</bg=green>';
                    break;
                case PATCH::UNPATCHED:
                    $status = '<error>Unpatched</error>';
                    break;
                default:
                    $status = 'Unknown';
            }
            $rows[] = array(
                $name,
                $status
            );
        }
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Name', 'Status'))
            ->setRows($rows)
            ->render();
    }
}