<?php

namespace MGA\Command\PHPUnit;

use MGA\Command\ScanCommand;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class TestCase
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    private $application = null;

    /**
     * @throws \RuntimeException
     * @return PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Console\Application
     */
    public function getApplication()
    {
        if ($this->application === null) {
            $this->application = new Application('Magento Guest Audit');
            $this->application->add(new ScanCommand);
        }
        return $this->application;
    }
}
