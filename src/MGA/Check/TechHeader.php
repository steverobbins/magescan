<?php
/**
 * Magento Guest Audit
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magento-guest-audit
 */

namespace MGA\Check;

use MGA\Request;

/**
 * Check for technical information exposed in the headers of a response
 */
class TechHeader
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
        $request  = new Request;
        $response = $request->fetch($url, array(
            CURLOPT_NOBODY => true
        ));
        $rows = array();
        foreach ($this->techHeader as $value) {
            $rows[] = array(
                $value,
                isset($response->header[$value])
                    ? $response->header[$value]
                    : ''
            );
        }
        return $rows;
    }
}
