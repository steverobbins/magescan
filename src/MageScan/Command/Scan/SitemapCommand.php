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

namespace MageScan\Command\Scan;

use MageScan\Check\Sitemap;
use MageScan\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan sitemap command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class SitemapCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:sitemap')
            ->setDescription('Check sitemap');
        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = [];
        $url = $this->getSitemapUrl();
        if ($url === false) {
            $result[] = '<error>Sitemap is not declared in robots.txt</error>';
            $url = $this->url . 'sitemap.xml';
        } else {
            $result[] = '<info>Sitemap is declared in robots.txt</info>';
        }
        $request = new Request;
        $response = $request->fetch($url, array(
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => true
        ));
        if ($response->code == 200) {
            $result[] = '<info>Sitemap is accessible:</info> ' . $url;
        } else {
            $result[] = '<error>Sitemap is not accessible:</error> ' . $url;
        }
        $this->out('Sitemap', $result);
    }

    /**
     * Parse the robots.txt text file to find the sitemap
     *
     * @return string|boolean
     */
    protected function getSitemapUrl()
    {
        $request = new Request;
        $response = $request->fetch($this->url . 'robots.txt');
        $sitemap = new Sitemap;
        $sitemap->setRequest($this->request);
        return $sitemap->getSitemapFromRobotsTxt($response);
    }
}
