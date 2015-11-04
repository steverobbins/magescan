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

use MageScan\Check\Patch\MageReport;

/**
 * Check for installed patches
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Patch extends AbstractCheck
{
    const PATCHED   = 1;
    const UNPATCHED = 2;
    const UNKNOWN   = 3;

    /**
     * Check all patches
     *
     * @param string $url
     *
     * @return array
     */
    public function checkAll($url)
    {
        $mageReport = new MageReport($url);
        $mageReport->setRequest($this->request);
        $results = $mageReport->checkAll();
        return $results;
    }
}
