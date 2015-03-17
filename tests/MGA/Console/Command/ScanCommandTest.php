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

namespace MGA\Tests\MGA\Console\Command;

use MGA\Console\Command\ScanCommand;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 */
class ScanCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a fully valid url
     */
    public function testHttpUrl()
    {
        $command  = new ScanCommand;
        $url      = 'http://www.example.com/';

        $cleanUrl = $command->cleanUrl($url);
        $this->assertSame($url, $cleanUrl);
    }

    /**
     * Test a fully valid url without the backslash
     */
    public function testHttpUrlNoBackslash()
    {
        $command  = new ScanCommand;
        $url      = 'http://www.example.com';

        $cleanUrl = $command->cleanUrl($url);
        $this->assertSame('http://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlNoHttp()
    {
        $command  = new ScanCommand;
        $url      = 'www.example.com';

        $cleanUrl = $command->cleanUrl($url);
        $this->assertSame('http://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlHttps()
    {
        $command  = new ScanCommand;
        $url      = 'https://www.example.com';

        $cleanUrl = $command->cleanUrl($url);
        $this->assertSame('https://www.example.com/', $cleanUrl);
    }

    /**
     * Test a url missing the http
     */
    public function testUrlSubDirectory()
    {
        $command  = new ScanCommand;
        $url      = 'www.example.com/store/';

        $cleanUrl = $command->cleanUrl($url);
        $this->assertSame('http://www.example.com/store/', $cleanUrl);
    }

    /**
     * Test a url with query params
     */
    public function testUrlQueryParams()
    {
        $command  = new ScanCommand;
        $url      = 'www.example.com?foo=bar';

        $cleanUrl = $command->cleanUrl($url);
        $this->assertSame('http://www.example.com/?foo=bar', $cleanUrl);
    }
}
