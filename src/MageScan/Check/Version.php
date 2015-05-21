<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Check;

/**
 * Scan for Magento edition and version
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Version
{
    const EDITION_ENTERPRISE   = 'Enterprise';
    const EDITION_PROFESSIONAL = 'Professional';
    const EDITION_COMMUNITY    = 'Community';

    /**
     * Guess Magento edition from license in public file
     *
     * @param \stdClass $response
     *
     * @return string
     */
    public function getMagentoEdition(\stdClass $response)
    {
        if ($response->code == 200) {
            preg_match('/@license.*/', $response->body, $match);
            if (isset($match[0])) {
                if (strpos($match[0], 'enterprise') !== false) {
                    return self::EDITION_ENTERPRISE;
                } elseif (strpos($match[0], 'commercial') !== false) {
                    return self::EDITION_PROFESSIONAL;
                }
                return self::EDITION_COMMUNITY;
            }
        }
        return 'Unknown';
    }

    /**
     * Guess Magento version from copyright in public file
     *
     * @param array  $response
     * @param string $edition
     *
     * @return string
     */
    public function getMagentoVersion(\stdClass $response, $edition)
    {
        if ($response->code == 200 && $edition != 'Unknown') {
            preg_match('/@copyright.*/', $response->body, $match);
            if (isset($match[0])
                && preg_match('/[0-9-]{4,}/', $match[0], $match)
                && isset($match[0])
            ) {
                return $this->getMagentoVersionByYear($match[0], $edition);
            }
        }
        return 'Unknown';
    }

    /**
     * Guess Magento version from copyright year and edition
     *
     * @param string $year
     * @param string $edition
     *
     * @return string
     */
    protected function getMagentoVersionByYear($year, $edition)
    {
        switch ($year) {
            case '2006-2015':
            case '2006-2014':
            case '2014':
                return $edition == self::EDITION_ENTERPRISE ?
                    '1.14' : '1.9';
            case 2013:
                return $edition == self::EDITION_ENTERPRISE ?
                    '1.13' : '1.8';
            case 2012:
                return ($edition == self::EDITION_ENTERPRISE || $edition == self::EDITION_PROFESSIONAL) ?
                    '1.12' : '1.7';
            case 2011:
                return ($edition == self::EDITION_ENTERPRISE || $edition == self::EDITION_PROFESSIONAL) ?
                    '1.11' : '1.6';
            case 2010:
                return ($edition == self::EDITION_ENTERPRISE || $edition == self::EDITION_PROFESSIONAL) ?
                    '1.9 - 1.10' : '1.4 - 1.5';
        }
        return 'Unknown';
    }
}
