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

use MageScan\Request;
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
        $this->assertSame('nginx/1.6.2', $parsed['Server']);
    }

    /**
     * Test a 404ing response
     */
    public function testFindMatchInResponseNotFound()
    {
        $response = new \stdClass();
        $response->code = 404;

        $request = new Request;
        $match   = $request->findMatchInResponse($response, '');
        $this->assertSame(false, $match);
    }

    /**
     * Test a good match
     */
    public function testFindMatchInResponseGood()
    {
        $response = new \stdClass();
        $response->code = 200;
        $response->body = 'Hello world!';

        $request = new Request;
        $match   = $request->findMatchInResponse($response, '/Hello (world)!/');
        $this->assertSame('world', $match);
    }

    /**
     * Test a bad match
     */
    public function testFindMatchInResponseBad()
    {
        $response = new \stdClass();
        $response->code = 200;
        $response->body = 'Hello world!';

        $request = new Request;
        $match   = $request->findMatchInResponse($response, '/Hello (foo)!/');
        $this->assertSame(false, $match);
    }

    /**
     * Test a good all match
     */
    public function testFindMatchInResponseAllGood()
    {
        $response = new \stdClass();
        $response->code = 200;
        $response->body = 'Hello world!';

        $request = new Request;
        $match   = $request->findMatchInResponse($response, '/(Hello) (world)!/', true);
        $this->assertSame('world', $match[2]);
    }
}
