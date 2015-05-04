<?php

namespace MGA\Command;

use Symfony\Component\Console\Tester\CommandTester;
use MGA\Command\PHPUnit\TestCase;

class ScanCommandTest extends TestCase
{
    public function testExecute()
    {
        $command = $this->getApplication()->find('scan');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'url'     => 'http://magento.local/',
            )
        );
        var_dump($commandTester->getDisplay());

        // Check pre defined vars
        // $edition = is_callable(array('\Mage', 'getEdition')) ? \Mage::getEdition() : 'Community';
        // $this->assertContains('magento.edition: ' . $edition, $commandTester->getDisplay());

        // $this->assertContains('magento.root: ' . $this->getApplication()->getMagentoRootFolder(), $commandTester->getDisplay());
        // $this->assertContains('magento.version: ' . \Mage::getVersion(), $commandTester->getDisplay());
        // $this->assertContains('magerun.version: ' . $this->getApplication()->getVersion(), $commandTester->getDisplay());

        // $this->assertContains('code', $commandTester->getDisplay());
        // $this->assertContains('foo.sql', $commandTester->getDisplay());
        // $this->assertContains('BAR: foo.sql.gz', $commandTester->getDisplay());
        // $this->assertContains('Magento Websites', $commandTester->getDisplay());
        // $this->assertContains('web/secure/base_url', $commandTester->getDisplay());
        // $this->assertContains('web/seo/use_rewrites => 1', $commandTester->getDisplay());
    }
}
