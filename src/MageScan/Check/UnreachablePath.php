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
use GuzzleHttp\Psr7\Response;

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
        $file = new File('unreachablepath.json');
        return $file->getJson();
    }

    /**
     * Test that paths are inaccessible
     *
     * @return array
     */
    public function checkPaths()
    {
        $result = [];
        $responses = $this->getRequest()->getMany($this->getPaths(), ['allow_redirects' => false]);
        foreach ($responses as $path => $response) {
            $result[] = $this->prepareResponse($path, $response);
        }
        return $result;
    }

    /**
     * Test that a path is inaccessible
     *
     * @param string $path
     *
     * @return array
     */
    public function checkPath($path)
    {
        $response = $this->getRequest()->get($path, ['allow_redirects' => false]);
        return $this->prepareResponse($path, $response);
    }

    /**
     * Build response array
     *
     * @param string   $path
     * @param Response $response
     *
     * @return string[]
     */
    protected function prepareResponse($path, Response $response)
    {
        return [
            $path,
            $response->getStatusCode(),
            $this->getUnreachableStatus($response)
        ];
    }

    /**
     * Get the status string for the given response
     *
     * @param Response $response
     *
     * @return mixed
     */
    protected function getUnreachableStatus(Response $response)
    {
        switch ($response->getStatusCode()) {
            case 200:
                return false;
            case 301:
            case 302:
                $headers = $response->getHeaders();
                return $headers['Location'][0];
        }
        return true;
    }
}
