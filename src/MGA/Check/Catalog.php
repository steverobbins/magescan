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
 * Check for installed modules
 */
class Catalog
{
    /**
     * Try to figure out how many categories there are in the store
     *
     * @param  string $url
     * @return integer|boolean
     */
    public function categoryCount($url)
    {
        $request = new Request;
        $response = $request->fetch($url . 'catalog/seo_sitemap/category', array(
            CURLOPT_FOLLOWLOCATION => true
        ));
        return $this->getCountFromResponse($response);
    }
    /**
     * Try to figure out how many products there are in the store
     *
     * @param  string $url
     * @return integer|boolean
     */
    public function productCount($url)
    {
        $request = new Request;
        $response = $request->fetch($url . 'catalog/seo_sitemap/product', array(
            CURLOPT_FOLLOWLOCATION => true
        ));
        return $this->getCountFromResponse($response);
    }

    /**
     * Parse out the count from the response
     * 
     * @param  \stdClass $response
     * @return integer|boolean
     */
    public function getCountFromResponse(\stdClass $response)
    {
        if ($response->code == 200) {
            if (preg_match('/Items? -?[0-9]+[a-z0-9- ]+ of ([0-9]+)/', $response->body, $match)
                && isset($match[1])
            ) {
                return $match[1];
            }
        }
        return false;
    }
}
