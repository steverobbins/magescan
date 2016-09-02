<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Dardo Guidobono <dardoguidobono@gmail.com>
 * @copyright 2016 Dardo Guidobono
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Check\Version;

use GuzzleHttp\Psr7\Response;
use MageScan\Check\AbstractCheck;
use MageScan\Check\Version;

/**
 * Scan for Magento edition and version via doc block style comment
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Dardo Guidobono <dardoguidobono@gmail.com>
 * @copyright 2016 Dardo Guidobono
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class DownloaderComment extends AbstractCheck
{
    /**
     * Guess magento edition and version
     *
     * @return array|boolean
     */
    public function getInfo()
    {
        $response = $this->getRequest()->get('downloader');
        $year = $this->getMagentoYear($response);
        if ($year) {
            $version = $this->getMagentoVersion($response);
            $edition = $this->getMagentoEditionByYearAndVersion($year, $version);
            return [$edition, $version];
        }
        return false;
    }

    /**
     * Guess Magento year from downloader url
     *
     * @param Response $response
     *
     * @return string|boolean
     */
    public function getMagentoYear(Response $response)
    {

        if ($response->getStatusCode() == 200) {
            preg_match('/([0-9]{4}).*Magento/', $response->getBody(), $match);
            if (isset($match[1])) {
                return $match[1];
            }
        }
        return false;
    }

    /**
     * Guess Magento version from downloader body
     *
     * @param Response       $response
     *
     * @return string|boolean
     */
    public function getMagentoVersion(Response $response)
    {
        if ($response->getStatusCode() == 200 ) {
            if ( preg_match('/([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(\.[0-9]{1,2})?)/', $response->getBody(), $match) ){
                return $match[1];
            }
        }
        return false;
    }

    /**
     * Guess Magento edition from copyright year and version
     *
     * @param string $year
     * @param string $version
     *
     * @return string
     */
    protected function getMagentoEditionByYearAndVersion($year, $version)
    {
        return 'Unknown';
    }
}
