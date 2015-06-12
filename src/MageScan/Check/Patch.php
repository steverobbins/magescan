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
        $results = array();
        $results['SUPEE-5344'] = $this->checkSupee5344($url);
        return $results;
    }

    /**
     * Check if SUPEE-5344 is patched
     *
     * @param string $url
     * @param string $admin
     *
     * @return boolean
     */
    public function checkSupee5344($url, $admin = 'admin')
    {
        $url = $this->trimUrl($url);
        $response = $this->getRequest()->fetch('https://shoplift.byte.nl/scan/' . $url . '/' . $admin . '.json', array(
            CURLOPT_FOLLOWLOCATION => true
        ));
        $body = json_decode($response->body);
        if (is_object($body)) {
            switch ($body->result) {
                case 'safe':
                    return self::PATCHED;
                case 'vuln':
                    return self::UNPATCHED;
            }
        }
        return self::UNKNOWN;
    }

    /**
     * Remove http from url
     *
     * @param string $url
     *
     * @return string
     */
    public function trimUrl($url)
    {
        return trim(preg_replace('/https?:\/+/', '', $url), '/');
    }
}
