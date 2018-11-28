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

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

/**
 * Make a cURL request to a url
 *
 * @method \GuzzleHttp\Psr7\Response get(string $path = null, array $params = [])
 * @method \GuzzleHttp\Psr7\Response[] getMany(array $paths, array $params = [])
 * @method \GuzzleHttp\Psr7\Response head(string $path = null, array $params = [])
 * @method \GuzzleHttp\Psr7\Response[] headMany(array $paths, array $params = [])
 * @method \GuzzleHttp\Psr7\Response post(string $path = null, array $params = [])
 * @method \GuzzleHttp\Psr7\Response[] postMany(array $paths, array $params = [])
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Request
{
    const REQUEST_TIMEOUT = 15.0;

    /**
     * If true, SSL does not have to be verified
     *
     * @var boolean
     */
    protected $insecure = false;

    /**
     * The base URL of the Magento application
     *
     * @var string|boolean
     */
    protected $url;

    /**
     * Client cache
     *
     * @var Client
     */
    protected $client;

    /**
     * Initialize request object
     *
     * @param string  $baseUri
     * @param boolean $verify
     */
    public function __construct($baseUri = false, $verify = true)
    {
        $this->url = $baseUri;
        $params = [
            'verify'   => $verify,
            'http_errors' => false,
            'allow_redirects' => [
                'max' => 20,
            ]
        ];
        if ($this->url !== false) {
            $params['base_uri'] = $this->url;
        }
        $this->client = new Client($params);
    }

    /**
     * Pass undefined requests to client
     *
     * @param string $method
     * @param arary  $args
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function __call($method, $args)
    {
        $paths  = isset($args[0]) ? $args[0] : false;
        $params = isset($args[1]) ? $args[1] : [];
        if (substr($method, -4) === 'Many') {
            $promises = [];
            foreach ($paths as $path) {
                $promises[$path] = $this->client->requestAsync(substr($method, 0, -4), '/' . $path, $params);
            }
            return Promise\unwrap($promises);
        }
        return $this->client->request($method, $paths ? '/' . $paths : null, $params);
    }

    /**
     * Get the base url of this request
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Manipulate header data into a parsable format
     *
     * @param string $rawData
     *
     * @return array
     */
    public function parseHeader($rawData)
    {
        $data = [];
        foreach (explode("\n", trim($rawData)) as $line) {
            $bits = explode(': ', $line);
            if (count($bits) > 1) {
                $key = $bits[0];
                unset($bits[0]);
                $data[$key] = trim(implode(': ', $bits));
            }
        }
        return $data;
    }

    /**
     * Parse out the count from the response
     *
     * @param string  $response
     * @param string  $pattern
     * @param boolean $returnAll
     *
     * @return string|array|boolean
     */
    public function findMatchInResponse($response, $pattern, $returnAll = false)
    {
        if (empty($response)) {
            return false;
        }
        if (preg_match($pattern, $response, $match)
            && (isset($match[1]) || $returnAll)
        ) {
            return $returnAll ? $match : $match[1];
        }
        return false;
    }
}
