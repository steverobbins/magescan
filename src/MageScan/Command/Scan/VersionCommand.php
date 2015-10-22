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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan version command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class VersionCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:version')
            ->setDescription('Get the version of a Magento installation');
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
        $version = new Version;
        $version->setRequest($this->request);
        $version = $version->getInfo($this->url);

        if ($input->getOption('json')) {
          $json = [
            "Edition" => $version[0] ?: 'Unknown',
            "Version" => $version[1] ?: 'Unknown'
          ];
          $output->write(json_encode($json));
        } else {
          $rows = array(
              array('Edition', $version[0] ?: 'Unknown'),
              array('Version', $version[1] ?: 'Unknown')
          );
          $this->writeHeader('Magento Information');
          $table = new Table($this->output);
          $table
              ->setHeaders(array('Parameter', 'Value'))
              ->setRows($rows)
              ->render();
        }
    }
}
