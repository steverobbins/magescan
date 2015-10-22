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

namespace MageScan;

/**
 * Url helper
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Url
{
    const DEFAULT_PROTOCOL = 'http';

    /**
     * Get the full, valid url from input
     *
     * @param string $input Dirty url input
     *
     * @return string
     */
    public function clean($input)
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
