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

namespace MGA\Magento;

use MGA\Request;

/**
 * Make a cURL request to a url
 */
class Module
{
    public $files = array(
        'skin/frontend/base/default/aw_islider/representations/default/style.css' => 'AW_Islider',
        'skin/frontend/base/default/css/magestore/sociallogin.css' => 'Magestore_Sociallogin',
    );

    public function checkForModules($url)
    {
        $modules = array();
        foreach ($this->files as $file => $name)
        {
            $response = Request::fetch($url . $file, array(
                CURLOPT_NOBODY         => true,
                CURLOPT_FOLLOWLOCATION => true
            ));
            if ($response->code == 200 && (!isset($modules[$name]) || $modules[$name] === false)) {
                $modules[$name] = true;
            } else {
                $modules[$name] = false;
            }
        }
        ksort($modules);
        return $modules;
    }
}
