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

use GuzzleHttp\Psr7\Response;
use MageScan\Request;

/**
 * Parse a sitemap
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Sitemap extends AbstractCheck
{
    /**
     * Parse the sitemap url out of a robots.txt contents
     *
     * @param Response $response
     *
     * @return string|boolean
     */
    public function getSitemapFromRobotsTxt(Response $response)
    {
        return $this->getRequest()->findMatchInResponse(
            $response->getBody()->getContents(),
            '/^(?!#+)\s*Sitemap:\s+(.*)$/mi'
        );
    }
}
