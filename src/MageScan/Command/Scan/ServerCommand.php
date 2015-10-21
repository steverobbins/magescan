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

use MageScan\Check\TechHeader;
use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Detect information about the server.
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class ServerCommand extends ScanCommand
{
    /**
     * Configure scan command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:server')
            ->setDescription('Check for information about the server.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeHeader('Server Technology');
        $techHeader = new TechHeader;
        $techHeader->setRequest($this->request);
        $values = $techHeader->getHeaders($this->url);
        if (empty($values)) {
            $this->output->writeln('No detectable technology was found');
            return;
        }
        $rows = array();
        foreach ($values as $key => $value) {
            $rows[] = array($key, $value);
        }
        $table = new Table($this->output);
        $table
            ->setHeaders(array('Key', 'Value'))
            ->setRows($rows)
            ->render();
    }
}