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

namespace MageScan\Check\Version;

use MageScan\Check\AbstractCheck;
use MageScan\Check\Version;
use Mvi\Check;

/**
 * Magento 2 has a controller that tells you the version
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class VersionController extends AbstractCheck
{
    /**
     * Check for version controller
     *
     * @return array|boolean
     */
    public function getInfo()
    {
        $response = $this->getRequest()->get('magento_version');
        if ($response->getStatusCode() == 200) {
            preg_match("/Magento\/([0-9]\.[0-9\.]+) \(([a-zA-Z]+)\)/", $response->getBody(), $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                $edition = $matches[2];
                $version = $matches[1];
                // An early versions of EE 2.0 would say it's 1.0
                if ($edition == 'Enterprise' && $version == '1.0') {
                    $version = '2.0';
                }
                return [$edition, $version];
            }
        }
        return false;
    }
}
