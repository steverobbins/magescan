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
 * Scan for category and product information
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Catalog extends AbstractCheck
{
    /**
     * Try to figure out how many categories there are in the store
     *
     * @param string $url
     *
     * @return string|boolean
     */
    public function categoryCount($url)
    {
        return $this->countEntity($url, 'category');
    }
    /**
     * Try to figure out how many products there are in the store
     *
     * @param string $url
     *
     * @return string|boolean
     */
    public function productCount($url)
    {
        return $this->countEntity($url, 'product');
    }

    /**
     * Count different entity types
     *
     * @param string $url
     * @param string $entity
     *
     * @return string|boolean
     */
    protected function countEntity($url, $entity)
    {
        $request = $this->getRequest();
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
