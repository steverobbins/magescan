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

namespace MGA;

/**
 * Parse a sitemap
 */
class Sitemap
{
    /**
     * Parse the sitemap url out of a robots.txt contents
     * 
     * @param  \stdClass      $robots
     * @return string|boolean
     */
    public static function getSitemapFromRobotsTxt($response)
    {
        if ($response->code != 200) {
            return false;
        }
        preg_match('/^(?!#+)\s*Sitemap: (.*)$/mi', $response->body, $match);
        if (!isset($match[1])) {
            return false;
        }
        return trim($match[1]);
    }
}
