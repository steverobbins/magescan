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

namespace MageScan\Test\Mga\Check\Version;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MageScan\Check\Version\DocComment;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on Magento version getter
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class DocCommentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for an existing but empty product.js
     *
     * @return void
     */
    public function testFileEmpty()
    {
        $response = $this->mockResponse(200, '');

        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame(false, $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame(false, $doccomment);
    }

    /**
     * Test for missing
     *
     * @return void
     */
    public function testFileMissing()
    {
        $response = $this->mockResponse(404, '');

        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame(false, $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame(false, $doccomment);
    }

    /**
     * Test EE 1.14
     *
     * @return void
     */
    public function testEnterprise114()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Enterprise', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.14', $doccomment);
    }

    /**
     * Test EE 1.13
     *
     * @return void
     */
    public function testEnterprise113()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Enterprise', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.13', $doccomment);
    }

    /**
     * Test EE 1.12
     *
     * @return void
     */
    public function testEnterprise112()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Enterprise', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.12', $doccomment);
    }

    /**
     * Test EE 1.12
     *
     * @return void
     */
    public function testProfessional112()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Professional', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.12', $doccomment);
    }

    /**
     * Test CE 1.9
     *
     * @return void
     */
    public function testCommunity19()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Community', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.9', $doccomment);
    }

    /**
     * Test CE 1.8
     *
     * @return void
     */
    public function testCommunity18()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Community', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.8', $doccomment);
    }

    /**
     * Test CE 1.7
     *
     * @return void
     */
    public function testCommunity17()
    {
        $body = <<<FILE
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
        $response = $this->mockResponse(200, $body);
        $doccomment = new DocComment;
        $edition = $doccomment->getEdition($response);
        $this->assertSame('Community', $edition);
        $doccomment = $doccomment->getVersion($response, $edition);
        $this->assertSame('1.7', $doccomment);
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
