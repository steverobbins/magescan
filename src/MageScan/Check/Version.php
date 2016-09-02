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
 * Scan for Magento edition and version
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Version extends AbstractCheck
{
    const EDITION_ENTERPRISE   = 'Enterprise';
    const EDITION_PROFESSIONAL = 'Professional';
    const EDITION_COMMUNITY    = 'Community';

    /**
     * Various ways we can sniff out the Magento version
     *
     * @var string[]
     */
    protected $versionCheck = [
        'FileHash',
        'DownloaderComment',
        'DocComment',
        'VersionController',
    ];

    /**
     * Guess Magento edition and version
     *
     * @return array
     */
    public function getInfo()
    {
        foreach ($this->versionCheck as $name) {
            $check = $this->getCheck($name);
            $result = $check->getInfo();
            if ($result !== false) {
                return $result;
            }
        }
        return [false, false];
    }

    /**
     * Get check object
     *
     * @param string $name
     *
     * @return AbstractCheck
     */
    protected function getCheck($name)
    {
        $class = '\\MageScan\\Check\\Version\\' . $name;
        $check = new $class;
        $check->setRequest($this->getRequest());
        return $check;
    }
}
