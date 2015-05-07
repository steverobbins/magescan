<?php

ini_set('display_errors', 1);

include '../vendor/autoload.php';

use MGA\Check\Catalog;
use MGA\Check\Module;
use MGA\Check\Sitemap;
use MGA\Check\TechHeader;
use MGA\Check\UnreachablePath;
use MGA\Check\Version;
use MGA\Request;
use MGA\Url;

if (isset($_GET['code'])) {
    $mgaUrl = new Url;
    $url = $mgaUrl->clean(urldecode($_GET['url']));
    $request = new Request;
    $response = $request->fetch($url, array(
        CURLOPT_NOBODY => true
    ));
    if (isset($response->header['Location'])) {
        $url = $response->header['Location'];
    }
    if (isset($response->header['location'])) {
        $url = $response->header['location'];
    }
    $mgaUrl = new Url;
    $url = $mgaUrl->clean($_GET['url']);
    call_user_func($_GET['code']);
}

function magentoinfo()
{
    global $url;
    $request = new Request;
    $response = $request->fetch(
        $url . 'js/varien/product.js',
        array(
            CURLOPT_FOLLOWLOCATION => true
        )
    );
    $version = new Version;
    $edition = $version->getMagentoEdition($response);
    $version = $version->getMagentoVersion($response, $edition);
    $rows = array(
        array('Edition', $edition),
        array('Version', $version)
    );
    respond(array('body' => $rows));
}

function modules()
{
    global $url;
    $rows = array();
    $module = new Module;
    $found = $notFound = array();
    foreach ($module->checkForModules($url) as $name => $exists) {
        if (!$exists) {
            continue;
        }
        $rows[] = array($name, 'Yes');
    }
    if (empty($rows)) {
        return respond(array('body' => array(array('No detectable modules were found'))));
    }
    respond(array(
        'head' => array('Module', 'Installed'),
        'body' => $rows
    ));
}

function catalog()
{
    global $url;
    $rows = array();
    $catalog  = new Catalog;
    $categoryCount = $catalog->categoryCount($url);
    $rows[] = array(
        'Categories',
        $categoryCount !== false ? $categoryCount : 'Unknown'
    );
    $productCount = $catalog->productCount($url);
    $rows[] = array(
        'Products',
        $productCount !== false ? $productCount : 'Unknown'
    );
    respond(array('body' => $rows));
}

function sitemap()
{
    global $url;
    $rows = array();
    $request = new Request;
    $response = $request->fetch($url . 'robots.txt');
    $sitemap = new Sitemap;
    $sitemapUrl  = $sitemap->getSitemapFromRobotsTxt($response);
    if ($sitemapUrl === false) {
        $rows[] = array('<span class="fail">Sitemap is not declared in robots.txt</span>');
        $sitemapUrl = $url . 'sitemap.xml';
    } else {
        $rows[] = array('<span class="pass">Sitemap is declared in robots.txt</span>');
    }
    $request = new Request;
    $response = $request->fetch($sitemapUrl, array(
        CURLOPT_NOBODY         => true,
        CURLOPT_FOLLOWLOCATION => true
    ));
    if ($response->code == 200) {
        $rows[] = array('<span class="pass">Sitemap is accessible</span>');
    } else {
        $rows[] = array('<span class="fail">Sitemap is not accessible</span>');
    }
    respond(array('body' => $rows));
}

function servertech()
{
    global $url;
    $rows = array();
    $techHeader = new TechHeader;
    $values = $techHeader->getHeaders($url);
    if (empty($values)) {
        $rows[] = array('No detectable technology was found');
    }
    foreach ($values as $key => $value) {
        $rows[] = array($key, $value);
    }
    respond(array('body' => $rows));
}

function unreachablepath()
{
    global $url;
    $rows = array();
    $unreachablePath = new UnreachablePath;
    $rows = $unreachablePath->checkPaths($url, true);
    foreach ($rows as &$result) {
        if ($result[2] === false) {
            $result[0] = '<a target="_blank" href="' . $url . $result[0] . '">' . $result[0] . '</a>';
            $result[2] = '<span class="fail">Fall</span>';
        } elseif ($result[2] === true) {
            $result[2] = '<span class="pass">Pass</span>';
        }
    }
    respond(array(
        'head' => array('Path', 'Response Code', 'Status'),
        'body' => $rows
    ));
}

function respond($data)
{
    echo json_encode($data);
}