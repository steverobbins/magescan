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
     * @return string|boolean
     */
    public function categoryCount($url)
    {
        return $this->countEntity($url, 'category');
    }
    /**
     * Try to figure out how many products there are in the store
     *
     * @param  string $url
     * @return string|boolean
     */
    public function productCount($url)
    {
        return $this->countEntity($url, 'product');
    }

    /**
     * Count different entity types
     *
     * @param  string $url
     * @param  string $entity
     * @return string|boolean
     */
    protected function countEntity($url, $entity)
    {
        $request = new Request;
        $response = $request->fetch($url . 'catalog/seo_sitemap/' . $entity, array(
            CURLOPT_FOLLOWLOCATION => true
        ));
        $match = $request->findMatchInResponse($response, '/-?[0-9]+[a-z0-9- ]+ of ([0-9]+)/');
        if (!$match) {
            $match = $request->findMatchInResponse($response, '/([0-9]+) Item\(s\)/');
        }
        return $match;
    }
}
