<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magescan
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Application;

$app = new Application('Mage Scan', '1.1');

$app->add(new ScanCommand);

$app->run();
