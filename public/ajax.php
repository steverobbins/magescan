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

require_once '../vendor/autoload.php';

$_SERVER['ALLOW_INSECURE'] = 1;

use MageScan\Http;

$code = isset($_GET['code']) ? $_GET['code'] : '';
$url = isset($_GET['url']) ? $_GET['url'] : '';

new Http($code, $url);
