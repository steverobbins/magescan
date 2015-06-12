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

use MageScan\Request;

/**
 * Defines some core check functionality
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
abstract class AbstractCheck
{
    /**
     * Request object
     *
     * @var \MageScan\Request
     */
    protected $request;

    /**
     * Get an instance of the Request object
     *
     * @return \MageScan\Request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->setRequest(new Request);
        }
        return $this->request;
    }

    /**
     * Set the cached request object
     *
     * @param \MageScan\Request $request
     *
     * @return AbstractCheck
     */
    public function setRequest(\MageScan\Request $request)
    {
        $this->request = $request;
        return $this;
    }
}
