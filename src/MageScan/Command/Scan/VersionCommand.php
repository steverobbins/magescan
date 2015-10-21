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

use MageScan\Check\Version;
use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get version information about a Magento site.
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class VersionCommand extends ScanCommand
{
    /**
     * Configure scan command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:version')
            ->setDescription('Get version information about a site.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeHeader('Magento Information');
        $version = new Version;
        $version->setRequest($this->request);
        $version = $version->getInfo($this->input->getArgument('url'));
        $rows = array(
            array('Edition', $version[0] ?: 'Unknown'),
            array('Version', $version[1] ?: 'Unknown')
        );
        $table = new Table($output);
        $table
            ->setHeaders(array('Parameter', 'Value'))
            ->setRows($rows)
            ->render();
    }
}