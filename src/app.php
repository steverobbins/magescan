#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MVD\Console\Command\ScanCommand;
use Symfony\Component\Console\Application;

$app = new Application();

$app->add(new ScanCommand);

$app->run();
