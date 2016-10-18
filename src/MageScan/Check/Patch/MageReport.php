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

namespace MageScan\Check\Patch;

use GuzzleHttp\Psr7\Response;
use MageScan\Check\AbstractCheck;
use MageScan\Check\Patch;
use MageScan\Request;

/**
 * Check for installed, provided by magereport.com
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class MageReport extends AbstractCheck
{
    const RESULT_SUCCESS = 'ok';
    const RESULT_FAIL    = 'fail';
    const BASE_URL       = 'https://www.magereport.com/';

    /**
     * The URL we're scanning
     *
     * @var string
     */
    private $url;

    /**
     * List of patches to check for
     *
     * @param array $patches
     */
    public $patches = [
        'scan/result/supee5344',
        'scan/result/supee5994',
        'scan/result/supee6285',
        'scan/result/supee6482',
        'scan/result/supee6788',
        'scan/result/supee7405',
        'scan/result/supee8788',
    ];

    /**
     * Set the URL
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Check all patches
     *
     * @return array
     */
    public function checkAll()
    {
        $results = [];
        $request = new Request(self::BASE_URL);
        $paths = $this->patches;
        array_walk($paths, function (&$value) {
            $value = $value . '?s=' . $this->url;
        });
        $responses = $request->getMany($paths);
        foreach ($responses as $path => $response) {
            $results[$this->getPatchName($path)] = $this->parseResponse($response);
        }
        return $results;
    }

    /**
     * Check if a given patch is installed
     *
     * @param string $endpoint
     *
     * @return integer
     */
    public function check($endpoint)
    {
        $request = new Request(self::BASE_URL);
        $response = $request->get($endpoint . '?s=' . $this->url);
        return $this->parseResponse($response);
    }

    /**
     * Get patch name from path
     *
     * @param string $path
     *
     * @return string
     */
    protected function getPatchName($path)
    {
        $bits = explode('?', $path);
        return str_replace('scan/result/supee', 'SUPEE-', $bits[0]);
    }

    /**
     * Derive if patched or not based on the response
     *
     * @param Response $response
     *
     * @return string
     */
    protected function parseResponse(Response $response)
    {
        $body = json_decode($response->getBody());
        if (is_object($body)) {
            switch ($body->result) {
                case self::RESULT_SUCCESS:
                    return Patch::PATCHED;
                case self::RESULT_FAIL:
                    return Patch::UNPATCHED;
            }
        }
        return Patch::UNKNOWN;
    }
}
