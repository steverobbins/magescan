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

namespace MageScan\Test\Mga\Check;

use MageScan\Check\UnreachablePath;
use PHPUnit_Framework_TestCase;

/**
 * Test parsing the sitemap out of a robots.txt file
 */
class UnreachablePathTest extends PHPUnit_Framework_TestCase
{
    /**
     * All urls should always be more
     */
    public function testGetAllPaths()
    {
        $unreachablePath = new UnreachablePath;
        $defaultPaths = $unreachablePath->getPaths();
        $allPaths = $unreachablePath->getPaths(true);

        $this->assertGreaterThan($defaultPaths, $allPaths);
        $this->assertContains($defaultPaths[0], $allPaths);
    }
}
