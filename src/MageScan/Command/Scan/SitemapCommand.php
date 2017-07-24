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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 0;

        $result = [];
        $url = $this->getSitemapUrl();
        if ($url === false) {
            $exitCode = 1;
            $result[] = '<error>Sitemap is not declared in robots.txt</error>';
            $url = $this->request->getUrl() . 'sitemap.xml';
        } else {
            $result[] = '<info>Sitemap is declared in robots.txt</info>';
        }
        $request = new Request(
            $url,
            $this->input->getOption('insecure')
        );
        $response = $request->get();
        if ($response->getStatusCode() == 200) {
            $result[] = '<info>Sitemap is accessible:</info> ' . $url;
        } else {
            $exitCode = 1;
            $result[] = '<error>Sitemap is not accessible:</error> ' . $url;
        }
        $this->out('Sitemap', $result);
        return $exitCode;
    }

    /**
     * Parse the robots.txt text file to find the sitemap
     *
     * @return string|boolean
     */
    protected function getSitemapUrl()
    {
        $request = new Request(
            $this->request->getUrl(),
            $this->input->getOption('insecure')
        );
        $response = $request->get('robots.txt');
        $sitemap = new Sitemap;
        $sitemap->setRequest($this->request);
        return $sitemap->getSitemapFromRobotsTxt($response);
    }
}
