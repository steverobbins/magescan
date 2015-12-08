<?php
/**
 * Mage Scan
 *
 * PHP version 5
 * 
 * @author    Steve Robbins <steve@steverobbins.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Test\MageScan;

use MageScan\Url;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a fully valid url
     */
    public function testHttpUrl()
    {
        $sampleUrl      = 'http://www.example.com/';

        $url = new Url;
        $cleanUrl = $url->clean($sampleUrl);
        $this->assertSame($sampleUrl, $cleanUrl);
    }

    /**
     * Test a fully valid url without the backslash
     */
    public function testHttpUrlNoBackslash()
    {
        $sampleUrl      = 'http://www.example.com';

        $url = new Url;
        $cleanUrl = $url->clean($sampleUrl);
        $this->assertSame('http://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlNoHttp()
    {
        $sampleUrl      = 'www.example.com';

        $url = new Url;
        $cleanUrl = $url->clean($sampleUrl);
        $this->assertSame('http://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url using https
     */
    public function testUrlHttps()
    {
        $sampleUrl      = 'https://www.example.com';

        $url = new Url;
        $cleanUrl = $url->clean($sampleUrl);
        $this->assertSame('https://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url using a sub directory
     */
    public function testUrlSubDirectory()
    {
        $sampleUrl      = 'www.example.com/store/';

        $url = new Url;
        $cleanUrl = $url->clean($sampleUrl);
        $this->assertSame('http://www.example.com/store/', $cleanUrl);
    }

    /**
     * Test a url that has query params
     */
    public function testUrlQueryParams()
    {
        $sampleUrl      = 'www.example.com?foo=bar';

        $url = new Url;
        $cleanUrl = $url->clean($sampleUrl);
        $this->assertSame('http://www.example.com/?foo=bar', $cleanUrl);
    }
}
