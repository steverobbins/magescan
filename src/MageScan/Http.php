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

use MageScan\Check\Catalog;
use MageScan\Check\Module;
use MageScan\Check\Patch;
use MageScan\Check\Sitemap;
use MageScan\Check\TechHeader;
use MageScan\Check\UnreachablePath;
use MageScan\Check\Version;
use MageScan\Request;
use MageScan\Url;

/**
 * Scan a Magento site using a web browser
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class Http
{
    /**
     * The URL we are scanning
     *
     * @var string
     */
    public $url;

    /**
     * Request object
     *
     * @var \MageScan\Request
     */
    protected $request;

    /**
     * Start a check
     *
     * @param string $code
     * @param string $url
     */
    public function __construct($code, $url)
    {
        $magescanUrl = new Url;
        $this->url     = $magescanUrl->clean(urldecode($url));
        $this->request = new Request($this->url, isset($_SERVER['ALLOW_INSECURE']));
        call_user_func([$this, 'check' . ucwords($code)]);
    }

    /**
     * Check for Magento version
     *
     * @return void
     */
    public function checkMagentoinfo()
    {
        $version = new Version;
        $version->setRequest($this->request);
        $version = $version->getInfo();
        $rows    = [
            ['Edition', $version[0] ?: 'Unknown'],
            ['Version', $version[1] ?: 'Unknown']
        ];
        $this->respond(['body' => $rows]);
    }

    /**
     * Check for installed modules
     *
     * @return void
     */
    public function checkModules()
    {
        $module = new Module;
        $module->setRequest($this->request);
        $this->respond(array_keys($module->getFiles()));
    }

    /**
     * Check for an installed module
     *
     * @return void
     */
    public function checkModulessingle()
    {
        $module = new Module;
        $module->setRequest($this->request);
        $file   = $_GET['path'];
        $files  = $module->getFiles();
        $result = $module->checkForModule($_GET['path']);
        if ($result) {
            $this->respond([
                isset($files[$file]) ? $files[$file] : '<!-- how did this happen -->'
            ]);
        }
    }

    /**
     * Check for install patches
     *
     * @return void
     */
    public function checkPatch()
    {
        $patch   = new Patch;
        $patch->setRequest($this->request);
        $patches = $patch->checkAll($this->url);
        $rows    = [];
        foreach ($patches as $name => $result) {
            switch ($result) {
                case PATCH::PATCHED:
                    $status = '<span class="pass">Patched</span class="pass">';
                    break;
                case PATCH::UNPATCHED:
                    $status = '<span class="fail">Unpatched</span class="fail">';
                    break;
                default:
                    $status = 'Unknown';
            }
            $rows[] = [
                $name,
                $status
            ];
        }
        $this->respond([
            'head' => ['Patch', 'Status'],
            'body' => $rows
        ]);
    }

    /**
     * Check for catalog information
     *
     * @return void
     */
    public function checkCatalog()
    {
        $rows    = [];
        $catalog = new Catalog;
        $catalog->setRequest($this->request);
        $categoryCount = $catalog->categoryCount();
        $rows[]        = [
            '<a href="' . $this->url. 'catalog/seo_sitemap/category" target="_blank">Categories</a>',
            $categoryCount !== false ? $categoryCount : 'Unknown'
        ];
        $productCount = $catalog->productCount();
        $rows[]       = [
            '<a href="' . $this->url . 'catalog/seo_sitemap/product" target="_blank">Products</a>',
            $productCount !== false ? $productCount : 'Unknown'
        ];
        $this->respond(['body' => $rows]);
    }

    /**
     * Check for a valid sitemap
     *
     * @return void
     */
    public function checkSitemap()
    {
        $rows       = [];
        $response   = $this->request->get('robots.txt');
        $sitemap    = new Sitemap;
        $sitemap->setRequest($this->request);
        $sitemapUrl = $sitemap->getSitemapFromRobotsTxt($response);
        if ($sitemapUrl === false) {
            $rows[] = ['<span class="fail">Sitemap is not declared in <a href="' . $this->url
                . 'robots.txt" target="_blank">robots.txt</a></span>'];
            $sitemapUrl = $this->url . 'sitemap.xml';
        } else {
            $rows[] = ['<span class="pass">Sitemap is declared in <a href="' . $this->url
                . 'robots.txt" target="_blank">robots.txt</a></span></span>'];
        }
        $response = $this->request->get($sitemapUrl);
        if ($response->code == 200) {
            $rows[] = ['<span class="pass"><a href="' . $sitemapUrl
                . '" target="_blank">Sitemap</a> is accessible</span>'];
        } else {
            $rows[] = ['<span class="fail"><a href="' . $sitemapUrl
                . '" target="_blank">Sitemap</a> is not accessible</span>'];
        }
        $this->respond(['body' => $rows]);
    }

    /**
     * Check for server technologies
     *
     * @return void
     */
    public function checkServertech()
    {
        $rows       = [];
        $techHeader = new TechHeader;
        $techHeader->setRequest($this->request);
        $values     = $techHeader->getHeaders();
        if (empty($values)) {
            $rows[] = ['No detectable technology was found'];
        }
        foreach ($values as $key => $value) {
            $rows[] = [$key, $value];
        }
        $this->respond(['body' => $rows]);
    }

    /**
     * Check for unreachable paths
     *
     * @return void
     */
    public function checkUnreachablepath()
    {
        $unreachablePath = new UnreachablePath;
        $unreachablePath->setRequest($this->request);
        $this->respond($unreachablePath->getPaths());
    }

    /**
     * Check for unreachable paths
     *
     * @return void
     */
    public function checkUnreachablepathsingle()
    {
        $unreachablePath = new UnreachablePath;
        $unreachablePath->setRequest($this->request);
        $result          = $unreachablePath->checkPath($_GET['path']);
        if ($result[2] === true) {
            return;
        }
        if ($result[2] === false) {
            $result[0] = '<a target="_blank" href="' . $this->url . $result[0] . '">' . $result[0] . '</a>';
            $result[2] = '<span class="fail">Reachable</span>';
        } elseif (substr($result[1], 0, 1) == 3) {
            if (substr($result[2], 0, 4) == 'http') {
                $newUrl = $result[2];
            } else {
                $newUrl = $this->url . substr($result[2], 1);
            }
            $result[0] = '<a target="_blank" href="' . $newUrl . '">' . $result[0] . '</a>';
            $result[2] = '<a target="_blank" href="' . $newUrl . '">Redirect</a>';
        }
        $this->respond($result);
    }

    /**
     * Send JSON response
     *
     * @param array $data
     *
     * @return void
     */
    public function respond(array $data)
    {
        echo json_encode($data);
    }
}
