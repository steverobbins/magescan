<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steve@steverobbins.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Test\Command;

use MageScan\Command\Scan\AllCommand;
use MageScan\Command\Scan\CatalogCommand;
use MageScan\Command\Scan\ModuleCommand;
use MageScan\Command\Scan\PatchCommand;
use MageScan\Command\Scan\ServerCommand;
use MageScan\Command\Scan\SitemapCommand;
use MageScan\Command\Scan\VersionCommand;
use MageScan\Command\Scan\UnreachableCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 */
class ScanAllCommandTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application;
        $application->add(new AllCommand);
        $application->add(new CatalogCommand);
        $application->add(new ModuleCommand);
        $application->add(new PatchCommand);
        $application->add(new ServerCommand);
        $application->add(new SitemapCommand);
        $application->add(new VersionCommand);
        $application->add(new UnreachableCommand);

        $command = $application->find('scan:all');
        $commandTester = new CommandTester($command);
        $result        =  $commandTester->execute(array(
            'command' => 'scan:all',
            'url'     => '127.0.0.1'
        ));
        $this->assertEquals(0, $result);
    }
}
