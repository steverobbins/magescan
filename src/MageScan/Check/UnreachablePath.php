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

use MageScan\File;

/**
 * Checks that files/folder aren't accessible
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class UnreachablePath extends AbstractCheck
{
    /**
     * Get all paths to be tested
     *
     * @return string[]
     */
    public function getPaths()
    {
        $file = new File('src/config/unreachablepath.json');
        return $file->getJson();
    }

    /**
     * Test that paths are inaccessible
     *
     * @param string $url
     *
     * @return array
     */
    public function checkPaths($url)
    {
        $result = array();
        foreach ($this->getPaths() as $path) {
            $result[] = $this->checkPath($url, $path);
        }
        return $result;
    }

    /**
     * Test that a path is inaccessible
     *
     * @param string $url
     * @param string $path
     *
     * @return array
     */
    public function checkPath($url, $path)
    {
        $response = $this->getRequest()->fetch($url . $path, array(
            CURLOPT_NOBODY => true
        ));
        return array(
            $path,
            $response->code,
            $this->getUnreachableStatus($url, $response)
        );
    }

    /**
     * Get the status string for the given response
     *
     * @param string    $url
     * @param \stdClass $response
     *
     * @return mixed
     */
    protected function getUnreachableStatus($url, \stdClass $response)
    {
        switch ($response->code) {
            case 200:
                return false;
            case 301:
            case 302:
                $redirect = $response->header['Location'];
                if ($redirect != $url) {
                    return $redirect;
                }
        }
        return true;
    }
}
