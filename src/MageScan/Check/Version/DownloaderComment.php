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
 * Scan for Magento edition and version via downloader url
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
     * @param Response $response
     *
     * @return string|boolean
     */
    public function getMagentoVersion(Response $response)
    {
        if ($response->getStatusCode() == 200) {
            if (preg_match('/([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(\.[0-9]{1,2})?)/', $response->getBody(), $match)) {
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
        switch ($year) {
            case 2008:
                if (in_array($version, [
                    '1',
                    '1.1.1',
                    '1.1.2',
                    '1.1.3',
                    '1.1.4',
                    '1.1.5',
                    '1.1.6',
                    '1.1.7',
                    '1.1.8',
                    '1.2.0',
                    '1.2.0.1',
                    '1.2.0.2',
                    '1.2.0.3',
                    '1.2.1',
                    '1.2.1.1',
                    '1.2.1.2',
                    '1.3.0',
                    '1.3.1',
                    '1.3.1.1',
                    '1.3.2',
                    '1.3.2.1',
                    '1.3.2.2',
                    '1.3.2.3',
                    '1.3.2.4',
                    '1.3.3.0',
                    '1.4.0.0',
                    '1.4.0.1',
                    '1.4.1.0',
                    '1.4.1.1',
                    '1.4.2.0',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
            case 2010:
                if (in_array($version, [
                    '1.5.0.0',
                    '1.5.0.1',
                    '1.5.1.0',
                    '1.6.0.0',
                    '1.6.1.0',
                    '1.6.2.0',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
            case 2012:
                if (in_array($version, [
                    '1.7.0.0',
                    '1.7.0.1',
                    '1.7.0.2',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
            case 2013:
                if (in_array($version, [
                    '1.8.0.0',
                    '1.8.1.0',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
            case 2014:
                if (in_array($version, [
                    '1.9.0.0',
                    '1.9.0.1',
                    '1.9.1.0',
                    '1.9.1.1',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
            case 2015:
                if (in_array($version, [
                    '1.9.1.1',
                    '1.9.2.0',
                    '1.9.2.1',
                    '1.9.2.2',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
            case 2016:
                if (in_array($version, [
                    '1.9.2.3',
                    '1.9.2.4',
                ])
                ) {
                    return Version::EDITION_COMMUNITY;
                }
                break;
        }
        return Version::EDITION_ENTERPRISE;
    }
}
