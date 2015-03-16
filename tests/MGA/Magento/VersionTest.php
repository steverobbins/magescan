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

namespace MGA\Tests\MGA\Magento;

use MGA\Magento\Version;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on Magento version getter
 */
class VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for an existing but empty product.js
     */
    public function testFileEmpty()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = '';

        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Unknown', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('Unknown', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testFileMissing()
    {
        $response = new \stdClass;
        $response->code = 404;
        $response->body = '';

        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Unknown', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('Unknown', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testEnterprise114()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Enterprise', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('1.14', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testEnterprise113()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Enterprise', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('1.13', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testEnterprise112()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Enterprise', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('1.12', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testCommunity19()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Community', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('1.9', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testCommunity18()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Community', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('1.8', $version);
    }

    /**
     * Test for a missing product.js
     */
    public function testCommunity17()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = <<<FILE
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $edition = Version::getMagentoEdition($response);
        $this->assertSame('Community', $edition);
        $version = Version::getMagentoVersion($response, $edition);
        $this->assertSame('1.7', $version);
    }
}
