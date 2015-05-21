<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Check;

use MageScan\Request;

/**
 * Check for technical information exposed in the headers of a response
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
     * @param  string $url
     * @return array
     */
    public function getHeaders($url)
    {
        $request  = $this->getRequest();
        $response = $request->fetch($url, array(
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
