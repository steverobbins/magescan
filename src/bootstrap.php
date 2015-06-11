<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MageScan\Command\ScanCommand;
use MageScan\Command\SelfUpdateCommand;
use Symfony\Component\Console\Application;

$app = new Application('Mage Scan', '1.5');

$app->add(new ScanCommand);
$app->add(new SelfUpdateCommand);

$app->run();
