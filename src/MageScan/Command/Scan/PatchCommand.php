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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan path command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class PatchCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:patch')
            ->setDescription('Get patch information');
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
        $rows  = [];
        $patch = new Patch;
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
            $rows[] = [
                $name,
                $status
            ];
        }
        $this->out('Patches', [[
            'type' => 'table',
            'data' => [
                ['Name', 'Status'],
                $rows
            ]
        ]]);
    }
}
