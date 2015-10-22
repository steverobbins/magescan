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

use MageScan\Check\Catalog;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan catalog command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class CatalogCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:catalog')
            ->setDescription('Get catalog information');
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

        $rows     = array();
        $catalog  = new Catalog;
        $catalog->setRequest($this->request);
        $categoryCount = $catalog->categoryCount($this->url);
        $productCount = $catalog->productCount($this->url);

        if ($input->getOption('json')) {
          $return = [
            "Categories" => $categoryCount !== false ? $categoryCount : 'Unknown',
            "Prodcuts" => $productCount !== false ? $productCount : 'Unknown'
          ];
          $output->write(json_encode($return));
        } else {
          $rows = [
            [
                'Categories',
                $categoryCount !== false ? $categoryCount : 'Unknown'
            ],[
              'Products',
              $productCount !== false ? $productCount : 'Unknown'
            ]
          ];
          $this->writeHeader('Catalog Information');
          $table = new Table($this->output);
          $table
              ->setHeaders(array('Type', 'Count'))
              ->setRows($rows)
              ->render();
        }


    }
}
