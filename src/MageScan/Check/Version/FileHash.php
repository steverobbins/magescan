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

namespace MageScan\Check\Version;

use MageScan\Check\Version;
use MageScan\Check\AbstractCheck;

/**
 * Scan for Magento edition and version via file md5 hash
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class FileHash extends AbstractCheck
{
    /**
     * List of files, hashes, and their versions
     *
     * @var array
     */
    protected $file = array(
        'js/mage/adminhtml/sales.js' => array(
            '839ead52e82a2041f937389445b8db04' => array(Version::EDITION_COMMUNITY, '1.3.3.0'),
            'bdacf81a3cf7121d7a20eaa266a684ec' => array(Version::EDITION_COMMUNITY, '1.5.1.0'),
            'd80c40eeef3ca62eb4243443fe41705e' => array(Version::EDITION_COMMUNITY, '1.5.0.1'),
            '48d609bb2958b93d7254c13957b704c4' => array(Version::EDITION_COMMUNITY, '1.6.1.0'),
            'a86ad3ba7ab64bf9b3d7d2b9861d93dc' => array(Version::EDITION_COMMUNITY, '1.0'),
            'a0436f1eee62dded68e0ec860baeb699' => array(Version::EDITION_COMMUNITY, '1.9.1.0'),
            '26c8fd113b4e51aeffe200ce7880b67a' => array(Version::EDITION_COMMUNITY, '1.8.0.0'),
            '5656a8c1c646afaaf260a130fe405691' => array(Version::EDITION_COMMUNITY, '1.8.1.0'),
            '95e730c4316669f2df71031d5439df21' => array(Version::EDITION_COMMUNITY, '1.1.0'),
            '17da0470950e8dd4b30ccb787b1605f5' => array(Version::EDITION_COMMUNITY, '1.1.6'),
            '5112f328e291234a943684928ebd3d33' => array(Version::EDITION_COMMUNITY, '1.1.7'),
            'c8dd0fd8fa3faa9b9f0dd767b5a2c995' => array(Version::EDITION_COMMUNITY, '1.9.1.1'),
            'a4296235ba7ad200dd042fa5200c11b0' => array(Version::EDITION_COMMUNITY, '1.6.0.0'),
            'd1bfb9f8d4c83e4a6a826d2356a97fd7' => array(Version::EDITION_COMMUNITY, '1.3.1.1'),
            '4422dffc16da547c671b086938656397' => array(Version::EDITION_COMMUNITY, '1.4.2.0'),
            '0e400488c83e63110da75534f49f23f3' => array(Version::EDITION_COMMUNITY, '1.3.2.1'),
        ),
        'skin/adminhtml/default/default/boxes.css' => array(
            '84b67457247969a206456565111c456b' => array(Version::EDITION_COMMUNITY, '1.1.4'),
            'd0511b190cdddf865cca7873917f9a69' => array(Version::EDITION_COMMUNITY, '1.1.1'),
            'a2c7f9ddda846ba76220d7bcbe85c985' => array(Version::EDITION_COMMUNITY, '1.2.1'),
            '1cbeca223c2e15dcaf500caa5d05b4ed' => array(Version::EDITION_COMMUNITY, '1.7.0.0'),
        ),
        'js/mage/adminhtml/tools.js' => array(
            'ea81bcf8d9b8fcddb27fb9ec7f801172' => array(Version::EDITION_COMMUNITY, '1.3.2.2'),
            '86bbebe2745581cd8f613ceb5ef82269' => array(Version::EDITION_COMMUNITY, '1.7.0.1'),
            'd594237950932b9a3948288a020df1ba' => array(Version::EDITION_COMMUNITY, '1.3.2.4'),
        ),
        'skin/frontend/rwd/default/css/styles.css' => array(
            'bf6c8e2ba2fc5162dd5187b39626a3a0' => array(Version::EDITION_COMMUNITY, '1.9.0.1'),
            '8a874fcb6cdcb82947ee4dbbe1822f3e' => array(Version::EDITION_COMMUNITY, '1.9.0.0'),
        ),
        'js/lib/flex.js' => array(
            '4040182326f3836f98acabfe1d507960' => array(Version::EDITION_COMMUNITY, '1.4.0.1'),
            'eb84fc6c93a9d27823dde31946be8767' => array(Version::EDITION_COMMUNITY, '1.4.0.0'),
        ),
        'js/varien/product.js' => array(
            '6af30941970891608b0be568896946db' => array(Version::EDITION_COMMUNITY, '1.2.0'),
        ),
        'js/mage/adminhtml/product.js' => array(
            'e887acfc2f7af09e04f8e99ac6f7180d' => array(Version::EDITION_COMMUNITY, '1.3.0'),
        ),
        'js/prototype/validation.js' => array(
            '295494d0966637bdd03e4ec17c2f338c' => array(Version::EDITION_COMMUNITY, '1.4.1.0'),
            'd3252becf15108532d21d45dced96d53' => array(Version::EDITION_COMMUNITY, '1.4.1.1'),
        ),
    );

    /**
     * Guess magento edition and version
     *
     * @param string $url
     *
     * @return array|boolean
     */
    public function getInfo($url)
    {
        foreach ($this->file as $file => $hash) {
            $response = $this->getRequest()->fetch(
                $url . $file,
                array(
                    CURLOPT_FOLLOWLOCATION => true
                )
            );
            $md5 = md5($response->body);
            if (isset($hash[$md5])) {
                return $hash[$md5];
            }
        }
        return false;
    }

    /**
     * Guess Magento edition from license in public file
     *
     * @param \stdClass $response
     *
     * @return string
     */
    public function getEdition(\stdClass $response)
    {
        if ($response->code == 200) {
            preg_match('/@license.*/', $response->body, $match);
            if (isset($match[0])) {
                if (strpos($match[0], 'enterprise') !== false) {
                    return Version::EDITION_ENTERPRISE;
                } elseif (strpos($match[0], 'commercial') !== false) {
                    return Version::EDITION_PROFESSIONAL;
                }
                return Version::EDITION_COMMUNITY;
            }
        }
        return false;
    }

    /**
     * Guess Magento version from copyright in public file
     *
     * @param \stdClass $response
     * @param string    $edition
     *
     * @return string
     */
    public function getVersion(\stdClass $response, $edition)
    {
        if ($response->code == 200 && $edition != false) {
            preg_match('/@copyright.*/', $response->body, $match);
            if (isset($match[0])
                && preg_match('/[0-9-]{4,}/', $match[0], $match)
                && isset($match[0])
            ) {
                return $this->getMagentoVersionByYear($match[0], $edition);
            }
        }
        return false;
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
                return $edition == Version::EDITION_ENTERPRISE ?
                    '1.14' : '1.9';
            case 2013:
                return $edition == Version::EDITION_ENTERPRISE ?
                    '1.13' : '1.8';
            case 2012:
                return ($edition == Version::EDITION_ENTERPRISE || $edition == Version::EDITION_PROFESSIONAL) ?
                    '1.12' : '1.7';
            case 2011:
                return ($edition == Version::EDITION_ENTERPRISE || $edition == Version::EDITION_PROFESSIONAL) ?
                    '1.11' : '1.6';
            case 2010:
                return ($edition == Version::EDITION_ENTERPRISE || $edition == Version::EDITION_PROFESSIONAL) ?
                    '1.9 - 1.10' : '1.4 - 1.5';
        }
        return 'Unknown';
    }
}
