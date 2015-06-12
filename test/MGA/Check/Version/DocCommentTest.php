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

namespace MageScan\Test\Mga\Check\Version;

use MageScan\Check\Version\DocComment;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on Magento version getter
 */
class DocCommentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for an existing but empty product.js
     */
    public function testFileEmpty()
    {
        $response = new \stdClass;
        $response->code = 200;
        $response->body = '';

        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame(false, $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame(false, $doccomment);
    }

    public function testFileMissing()
    {
        $response = new \stdClass;
        $response->code = 404;
        $response->body = '';

        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame(false, $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame(false, $doccomment);
    }

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
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Enterprise', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.14', $doccomment);
    }

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
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Enterprise', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.13', $doccomment);
    }

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
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Enterprise', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.12', $doccomment);
    }

    public function testProfessional112()
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
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */
if(typeof Product=='undefined') {
    var Product = {};
FILE;
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Professional', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.12', $doccomment);
    }

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
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Community', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.9', $doccomment);
    }

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
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Community', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.8', $doccomment);
    }

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
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Community', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.7', $doccomment);
    }
}
