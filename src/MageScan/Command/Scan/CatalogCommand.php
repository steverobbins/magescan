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
        $rows     = [];
        $catalog  = new Catalog;
        $catalog->setRequest($this->request);
        $categoryCount = $catalog->categoryCount();
        $rows[] = [
            'Categories',
            $categoryCount !== false ? $categoryCount : 'Unknown'
        ];
        $productCount = $catalog->productCount();
        $rows[] = [
            'Products',
            $productCount !== false ? $productCount : 'Unknown'
        ];
        $this->out('Catalog Information', [[
            'type' => 'table',
            'data' => [
                ['Type', 'Count'],
                $rows
            ]
        ]]);
    }
}
