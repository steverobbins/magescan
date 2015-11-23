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
 * Check for technical information exposed in the headers of a response
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class TechHeader extends AbstractCheck
{
    /**
     * Headers that provide information about the technology used
     *
     * @var array
     */
    protected $techHeader = [
        'Server',
        'Via',
        'X-Mod-Pagespeed',
        'X-Page-Speed',
        'X-Powered-By',
    ];

    /**
     * Crawl the url's headers
     *
     * @return array
     */
    public function getHeaders()
    {
        $response = $this->getRequest()->get();
        $rows = [];
        $headers = $response->getHeaders();
        foreach ($this->techHeader as $value) {
            if (isset($headers[$value])) {
                $rows[$value] = $headers[$value][0];
            }
        }
        return $rows;
    }
}
