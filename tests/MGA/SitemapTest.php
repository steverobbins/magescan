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

namespace MGA\Tests;

use MGA\Sitemap;
use PHPUnit_Framework_TestCase;

/**
 * Test parsing the sitemap out of a robots.txt file
 */
class SitemapTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for an existing but empty robots.txt
     */
    public function testFileEmpty()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = '';

        $sitemap = Sitemap::getSitemapFromRobotsTxt($response);
        $this->assertSame(false, $sitemap);
    }

    /**
     * Test for a missing robots.txt
     */
    public function testFileMissing()
    {
        $response = new \stdClass;
        $response->code = 404;
        $response->body = '';

        $sitemap = Sitemap::getSitemapFromRobotsTxt($response);
        $this->assertSame(false, $sitemap);
    }

    /**
     * Test for a normal robots.txt
     */
    public function testGoodSitemap()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
User-agent: *
Allow: /
Sitemap: http://www.example.com/sitemap.xml
FILE;

        $sitemap = Sitemap::getSitemapFromRobotsTxt($response);
        $this->assertSame('http://www.example.com/sitemap.xml', $sitemap);
    }

    /**
     * Test for a normal robots.txt with sitemap commented out
     */
    public function testCommentOut()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
User-agent: *
Allow: /
#Sitemap: http://www.example.com/sitemap.xml
FILE;

        $sitemap = Sitemap::getSitemapFromRobotsTxt($response);
        $this->assertSame(false, $sitemap);
    }
}
