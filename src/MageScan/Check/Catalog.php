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
     * @return string|boolean
     */
    public function categoryCount()
    {
        return $this->countEntity('category');
    }

    /**
     * Try to figure out how many products there are in the store
     *
     * @return string|boolean
     */
    public function productCount()
    {
        return $this->countEntity('product');
    }

    /**
     * Count different entity types
     *
     * @param string $entity
     *
     * @return string|boolean
     */
    protected function countEntity($entity)
    {
        $request      = $this->getRequest();
        $response     = $request->get('catalog/seo_sitemap/' . $entity);
        $responseBody = $response->getBody()->getContents();
        $match        = $request->findMatchInResponse($responseBody, '/-?[0-9]+[a-z0-9- ]+ of ([0-9]+)/');
        if (!$match) {
            $match = $request->findMatchInResponse($responseBody, '/([0-9]+) Item\(s\)/');
        }
        return $match;
    }
}
