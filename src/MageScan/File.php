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
 * Access the file system
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class File
{
    /**
     * Root directory of app
     *
     * @var string
     */
    private $root;

    /**
     * This file's locations
     *
     * @var array
     */
    private $paths = array();

    /**
     * Initialize the root dir and set the path
     *
     * @param string $path
     */
    public function __construct($filename)
    {
        $this->root = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        $this->paths[] = $this->root . 'src' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $filename;
        $homePath = getenv("HOME").'/.magescan/config/'.$filename;
        if (file_exists($homePath)) {
            $this->paths[] = $homePath;
        }
    }

    /**
     * Returns the json file contents as an array
     *
     * @return array
     */
    public function getJson()
    {
        $return = array();
        foreach ($this->paths as $path) {
            $pathData = json_decode(file_get_contents($path), true);
            $return = array_merge($return, $pathData);
        }

        return $return;
    }
}
