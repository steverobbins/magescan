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
 * Make a cURL request to a url
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
    /**
     * If true, SSL does not have to be verified
     *
     * @var boolean
     */
    protected $insecure = false;

    /**
     * Mark the request as insecure which will prevent ssl validation
     *
     * @param boolean $flag
     *
     * @return Request
     */
    public function setInsecure($flag)
    {
        $this->insecure = (boolean) $flag;
        return $this;
    }

    /**
     * Create a curl request for a given url
     *
     * @param string $url
     * @param array  $params
     *
     * @return \stdClass
     */
    public function fetch($url, array $params = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if ($this->insecure) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        foreach ($params as $key => $value) {
            curl_setopt($ch, $key, $value);
        }
        $response     = curl_exec($ch);
        $result       = new \stdClass;
        $result->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize   = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $result->header = $this->parseHeader(substr($response, 0, $headerSize));
        $result->body   = substr($response, $headerSize);
        return $result;
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
        $data = array();
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
     * @param \stdClass $response
     * @param string    $pattern
     * @param boolean   $returnAll
     *
     * @return string|array|boolean
     */
    public function findMatchInResponse(\stdClass $response, $pattern, $returnAll = false)
    {
        if ($response->code == 200) {
            if (preg_match($pattern, $response->body, $match)
                && (isset($match[1]) || $returnAll)
            ) {
                return $returnAll ? $match : $match[1];
            }
        }
        return false;
    }
}
