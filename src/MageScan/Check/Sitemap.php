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
 * Parse a sitemap
 */
class Sitemap extends AbstractCheck
{
    /**
     * Parse the sitemap url out of a robots.txt contents
     *
     * @param  \stdClass      $response
     * @return string|boolean
     */
    public function getSitemapFromRobotsTxt($response)
    {
        return $this->getRequest()->findMatchInResponse($response, '/^(?!#+)\s*Sitemap: (.*)$/mi');
    }
}
