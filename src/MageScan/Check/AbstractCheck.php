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
 * Defines some core check functionality
 */
abstract class AbstractCheck
{
    /**
     * @var \MageScan\Request
     */
    protected $request;

    /**
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
     * @param \MageScan\Request $request
     */
    public function setRequest(\MageScan\Request $request)
    {
        $this->request = $request;
    }
}
