<?php
/**
 * Magento Guest Audit
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magento-guest-audit
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MGA\Command\ScanCommand;
use Symfony\Component\Console\Application;

$app = new Application('Magento Guest Audit', '0.7.7');

$app->add(new ScanCommand);

$app->run();
