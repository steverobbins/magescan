<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MGA\Console\Command\ScanCommand;
use Symfony\Component\Console\Application;

$app = new Application();

$app->add(new ScanCommand);

$app->run();
