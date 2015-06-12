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

namespace MageScan\Check;

/**
 * Checks that files/folder aren't accessible
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class UnreachablePath extends AbstractCheck
{
    /**
     * List of paths that we shouldn't be able to access
     *
     * @var array
     */
    protected $unreachablePathDefault = array(
        '.git/config',
        '.svn/entries',
        'admin',
        'app/etc/local.xml',
        'composer.json',
        'downloader/index.php',
        'phpinfo.php',
        'phpmyadmin',
        'var/log/exception.log',
        'var/log/system.log',
    );

    /**
     * Get all paths to be tested
     *
     * @param boolean $all
     *
     * @return string[]
     */
    public function getPaths($all = false)
    {
        $paths = $this->unreachablePathDefault;
        if ($all) {
            $paths += $this->unreachablePathMore;
        }
        sort($paths);
        return $paths;
    }

    /**
     * More paths that we shouldn't be able to access
     *
     * @var array
     */
    protected $unreachablePathMore = array(
        '.bzr/',
        '.cvs/',
        '.git/',
        '.git/refs/',
        '.gitignore',
        '.idea',
        '.hg/',
        '.svn/',
        'app/etc/enterprise.xml',
        'chive',
        'composer.lock',
        'magmi/',
        'magmi/conf/magmi.ini',
        'magmi/web/magmi.php',
        'info.php',
        'p.php',
        'README.txt',
        'README.md',
        'shell/',
        'var/export/',
        'var/export/export_all_products.csv',
        'var/export/export_product_stocks.csv',
        'var/export/export_customers.csv',
        'var/log/',
        'var/log/payment_authnetcim.log',
        'var/log/payment_authorizenet.log',
        'var/log/payment_authorizenet_directpost.log',
        'var/log/payment_cybersource_soap.log',
        'var/log/payment_ogone.log',
        'var/log/payment_payflow_advanced.log',
        'var/log/payment_payflow_link.log',
        'var/log/payment_paypal_billing_agreement.log',
        'var/log/payment_paypal_direct.log',
        'var/log/payment_paypal_express.log',
        'var/log/payment_paypal_standard.log',
        'var/log/payment_paypaluk_express.log',
        'var/log/payment_pbridge.log',
        'var/log/payment_verisign.log',
        'var/report/',
    );

    /**
     * Test that paths are inaccessible
     *
     * @param string  $url
     * @param boolean $all
     *
     * @return array
     */
    public function checkPaths($url, $all = false)
    {
        $result = array();
        foreach ($this->getPaths($all) as $path) {
            $result[] = $this->checkPath($url, $path);
        }
        return $result;
    }

    /**
     * Test that a path is inaccessible
     *
     * @param string $url
     * @param string $path
     *
     * @return array
     */
    public function checkPath($url, $path)
    {
        $response = $this->getRequest()->fetch($url . $path, array(
            CURLOPT_NOBODY => true
        ));
        return array(
            $path,
            $response->code,
            $this->getUnreachableStatus($url, $response)
        );
    }

    /**
     * Get the status string for the given response
     *
     * @param string    $url
     * @param \stdClass $response
     *
     * @return mixed
     */
    protected function getUnreachableStatus($url, \stdClass $response)
    {
        switch ($response->code) {
            case 200:
                return false;
            case 301:
            case 302:
                $redirect = $response->header['Location'];
                if ($redirect != $url) {
                    return $redirect;
                }
        }
        return true;
    }
}
