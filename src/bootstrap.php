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

require_once __DIR__ . '/../vendor/autoload.php';

use MageScan\Command\Scan\AllCommand;
use MageScan\Command\Scan\CatalogCommand;
use MageScan\Command\Scan\ModuleCommand;
use MageScan\Command\Scan\PatchCommand;
use MageScan\Command\Scan\ServerCommand;
use MageScan\Command\Scan\SitemapCommand;
use MageScan\Command\Scan\VersionCommand;
use MageScan\Command\Scan\UnreachableCommand;
use MageScan\Command\SelfUpdateCommand;
use Symfony\Component\Console\Application;

$app = new Application('Mage Scan', '1.11.3');

$app->add(new AllCommand);
$app->add(new VersionCommand);
$app->add(new ModuleCommand);
$app->add(new CatalogCommand);
$app->add(new PatchCommand);
$app->add(new SitemapCommand);
$app->add(new ServerCommand);
$app->add(new UnreachableCommand);
$app->add(new SelfUpdateCommand);

$app->run();
