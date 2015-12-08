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

namespace MageScan\Test\Mga\Check;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MageScan\Check\Sitemap;
use MageScan\Request;
use PHPUnit_Framework_TestCase;

/**
 * Test parsing the sitemap out of a robots.txt file
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class SitemapTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for an existing but empty robots.txt
     *
     * @return void
     */
    public function testFileEmpty()
    {
        $sitemap = $this->mockSitemap(200, '');
        $this->assertSame(false, $sitemap);
    }

    /**
     * Test for a missing robots.txt
     *
     * @return void
     */
    public function testFileMissing()
    {
        $sitemap = $this->mockSitemap(404, '');
        $this->assertSame(false, $sitemap);
    }

    /**
     * Test for a normal robots.txt
     *
     * @return void
     */
    public function testGoodSitemap()
    {
        $body = <<<FILE
User-agent: *
Allow: /
Sitemap: http://www.example.com/sitemap.xml
FILE;
        $sitemap = $this->mockSitemap(200, $body);
        $this->assertSame('http://www.example.com/sitemap.xml', $sitemap);
    }

    /**
     * Test for a normal robots.txt with sitemap commented out
     *
     * @return void
     */
    public function testCommentOut()
    {
        $body = <<<FILE
User-agent: *
Allow: /
#Sitemap: http://www.example.com/sitemap.xml
FILE;
        $sitemap = $this->mockSitemap(200, $body);
        $this->assertSame(false, $sitemap);
    }

    /**
     * Mock a sitemap response
     *
     * @param integer $status
     * @param string  $body
     *
     * @return boolean|string
     */
    protected function mockSitemap($status, $body)
    {
        $mock = new MockHandler([
            new Response($status, [], $body),
        ]);
        $handler  = HandlerStack::create($mock);
        $client   = new Client(['handler' => $handler, 'http_errors' => false,]);
        $response = $client->request('GET', '/');
        $sitemap  = new Sitemap;
        return $sitemap->setRequest(new Request())->getSitemapFromRobotsTxt($response);
    }
}
