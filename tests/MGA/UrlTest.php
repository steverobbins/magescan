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

namespace MGA\Tests\MGA;

use MGA\Url;
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
        $url      = 'http://www.example.com/';

        $cleanUrl = Url::clean($url);
        $this->assertSame($url, $cleanUrl);
    }

    /**
     * Test a fully valid url without the backslash
     */
    public function testHttpUrlNoBackslash()
    {
        $url      = 'http://www.example.com';

        $cleanUrl = Url::clean($url);
        $this->assertSame('http://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlNoHttp()
    {
        $url      = 'www.example.com';

        $cleanUrl = Url::clean($url);
        $this->assertSame('http://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlHttps()
    {
        $url      = 'https://www.example.com';

        $cleanUrl = Url::clean($url);
        $this->assertSame('https://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlSubDirectory()
    {
        $url      = 'www.example.com/store/';

        $cleanUrl = Url::clean($url);
        $this->assertSame('http://www.example.com/store/', $cleanUrl);
    }

    /**
     * Test a url with query params
     */
    public function testUrlQueryParams()
    {
        $url      = 'www.example.com?foo=bar';

        $cleanUrl = Url::clean($url);
        $this->assertSame('http://www.example.com/?foo=bar', $cleanUrl);
    }
}
