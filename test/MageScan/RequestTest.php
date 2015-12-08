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

namespace MageScan\Test\MageScan;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MageScan\Request;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getting the server from the headers
     *
     * @return void
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
     *
     * @return void
     */
    public function testFindMatchInResponseNotFound()
    {
        $response = $this->mockResponse(404, '');

        $request = new Request;
        $match   = $request->findMatchInResponse($response->getBody()->getContents(), '');
        $this->assertSame(false, $match);
    }

    /**
     * Test a good match
     *
     * @return void
     */
    public function testFindMatchInResponseGood()
    {
        $body = 'Hello world!';
        $response = $this->mockResponse(200, $body);

        $request = new Request;
        $match   = $request->findMatchInResponse($response->getBody()->getContents(), '/Hello (world)!/');
        $this->assertSame('world', $match);
    }

    /**
     * Test a bad match
     *
     * @return void
     */
    public function testFindMatchInResponseBad()
    {
        $body = 'Hello world!';
        $response = $this->mockResponse(200, $body);

        $request = new Request;
        $match   = $request->findMatchInResponse($response->getBody()->getContents(), '/Hello (foo)!/');
        $this->assertSame(false, $match);
    }

    /**
     * Test a good all match
     *
     * @return void
     */
    public function testFindMatchInResponseAllGood()
    {
        $body = 'Hello world!';
        $response = $this->mockResponse(200, $body);

        $request = new Request;
        $match   = $request->findMatchInResponse($response->getBody()->getContents(), '/(Hello) (world)!/', true);
        $this->assertSame('world', $match[2]);
    }

    /**
     * Mock a response
     *
     * @param integer $status
     * @param string  $body
     *
     * @return boolean|string
     */
    protected function mockResponse($status, $body)
    {
        $mock = new MockHandler([
            new Response($status, [], $body),
        ]);
        $handler  = HandlerStack::create($mock);
        $client   = new Client(['handler' => $handler, 'http_errors' => false,]);
        return $client->request('GET', '/');
    }
}
