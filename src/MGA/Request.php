<?php
/**
 * Magento Guest Audit
 *
 * PHP version 5
 * 
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magento-guest-audit
 */

namespace MGA;

/**
 * Make a cURL request to a url
 */
class Request
{
    /**
     * Create a curl request for a given url
     * 
     * @param  string   $url
     * @param  array    $params
     * @return stdClass
     */
	public function make($url, array $params = array())
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
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
     * @param  string $rawData
     * @return array
     */
    protected function parseHeader($rawData)
    {
        $data = array();
        foreach (explode("\r\n", $rawData) as $line) {
            $bits = explode(': ', $line);
            if (count($bits) == 2) {
                $data[$bits[0]] = $bits[1];
            }
        }
        return $data;
    }
}
