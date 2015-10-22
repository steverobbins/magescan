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
        $checker = new Check($url);
        $info    = $checker->getInfo();
        if ($info === false) {
            return false;
        }
        $edition  = key($info);
        $versions = $info[$edition];
        return [$edition, implode(', ', $versions)];
    }
}
