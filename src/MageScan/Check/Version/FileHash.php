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

use MageScan\File;
use MageScan\Check\Version;
use MageScan\Check\AbstractCheck;

/**
 * Scan for Magento edition and version via file md5 hash
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class FileHash extends AbstractCheck
{
    /**
     * Guess magento edition and version
     *
     * @param string $url
     *
     * @return array|boolean
     */
    public function getInfo($url)
    {
        $file = new File('src/config/version/hash.json');
        foreach ($file->getJson() as $path => $hash) {
            $response = $this->getRequest()->fetch(
                $url . $path,
                array(
                    CURLOPT_FOLLOWLOCATION => true
                )
            );
            $md5 = md5($response->body);
            if (isset($hash[$md5])) {
                return $hash[$md5];
            }
        }
        return false;
    }
}
