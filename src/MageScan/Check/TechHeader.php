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
 * Check for technical information exposed in the headers of a response
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class TechHeader extends AbstractCheck
{
    /**
     * Headers that provide information about the technology used
     *
     * @var array
     */
    protected $techHeader = array(
        'Server',
        'Via',
        'X-Mod-Pagespeed',
        'X-Page-Speed',
        'X-Powered-By',
    );

    /**
     * Crawl the url's headers
     *
     * @param string $url
     *
     * @return array
     */
    public function getHeaders($url)
    {
        $response = $this->getRequest()->fetch($url, array(
            CURLOPT_NOBODY => true
        ));
        $rows = array();
        foreach ($this->techHeader as $value) {
            if (isset($response->header[$value])) {
                $rows[$value] = $response->header[$value];
            }
        }
        return $rows;
    }
}
