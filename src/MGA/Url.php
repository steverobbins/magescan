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
 * Url helper
 */
class Url
{
    const DEFAULT_PROTOCOL = 'http';

    /**
     * Get the full, valid url from input
     * This could probably written better
     * 
     * @param  string $input
     * @return string
     */
    public static function clean($input)
    {
        $bits = explode('://', $input);
        if (count($bits) > 1) {
            $protocol = $bits[0];
            unset($bits[0]);
        } else {
            $protocol = self::DEFAULT_PROTOCOL;
        }
        $url  = implode($bits);
        $bits = explode('?', $url);
        if (substr($bits[0], -1) != '/') {
            $bits[0] .= '/';
        }
        return $protocol . '://' . implode('?', $bits);
    }
}
