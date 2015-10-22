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

namespace MageScan\Check\Patch;

use MageScan\Check\AbstractCheck;
use MageScan\Check\Patch;

/**
 * Check for installed, provided by magereport.com
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class MageReport extends AbstractCheck
{
    const RESULT_SUCCESS = 'ok';
    const RESULT_FAIL    = 'fail';

    /**
     * The URL we're scanning
     *
     * @var string
     */
    private $url;

    /**
     * List of patches to check for
     *
     * @param array $patches
     */
    public $patches = [
        'SUPEE-5344' => 'https://www.magereport.com/scan/result/supee5344',
        'SUPEE-5994' => 'https://www.magereport.com/scan/result/supee5994',
        'SUPEE-6285' => 'https://www.magereport.com/scan/result/supee6285',
        'SUPEE-6482' => 'https://www.magereport.com/scan/result/supee6482',
    ];

    /**
     * Set the URL
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Check all patches
     *
     * @return array
     */
    public function checkAll()
    {
        $results = array();
        foreach ($this->patches as $name => $endpoint) {
            $results[$name] = $this->check($endpoint);
        }
        return $results;
    }

    /**
     * Check if a given patch is installed
     *
     * @param string $endpoint
     *
     * @return integer
     */
    public function check($endpoint)
    {
        $response = $this->getRequest()->fetch($endpoint . '?s=' . $this->url, [
            CURLOPT_FOLLOWLOCATION => true
        ]);
        $body = json_decode($response->body);
        if (is_object($body)) {
            switch ($body->result) {
                case self::RESULT_SUCCESS:
                    return Patch::PATCHED;
                case self::RESULT_FAIL:
                    return Patch::UNPATCHED;
            }
        }
        return Patch::UNKNOWN;
    }
}
