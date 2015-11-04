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
 * Check for installed modules
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Module extends AbstractCheck
{

    /**
     * Check for module files that exist in a url
     *
     * @return array
     */
    public function checkForModules()
    {
        $modules = [];
        $responses = $this->getRequest()->getMany($this->getFiles());
        foreach ($responses as $path => $response) {
            if ($response->getStatusCode == 200 && (!isset($modules[$name]) || $modules[$name] === false)) {
                $modules[$name] = true;
            } else {
                $modules[$name] = false;
            }
        }
        ksort($modules);
        return $modules;
    }

    /**
     * Check for a module file that exist in a url
     *
     * @param string $file
     *
     * @return boolean
     */
    public function checkForModule($file)
    {
        $response = $this->getRequest()->get($file);
        return $response->getStatusCode() == 200;
    }

    /**
     * Get modules files as array
     *
     * @return array
     */
    public function getFiles()
    {
        $file = new File('module.json');
        return $file->getJson();
    }
}
