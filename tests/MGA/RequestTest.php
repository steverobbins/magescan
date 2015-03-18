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

use MGA\Request;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getting the server from the headers
     */
    public function testGetServer()
    {
        $headers = <<<HEADERS
HTTP/1.1 200 OK
Server: nginx/1.6.2
Date: Wed, 18 Mar 2015 01:33:55 GMT
HEADERS;

        $request = new Request;
        $parsed = $request->parseHeader($headers);
        var_dump($parsed);
        $this->assertSame('nginx/1.6.2', $parsed['Server']);
    }
}
