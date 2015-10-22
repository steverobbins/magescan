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

        $url = $this->getSitemapUrl($input->getOption('json'));
        $request = new Request;
        $response = $request->fetch($url, array(
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => true
        ));

        if ($input->getOption('json')) {
          if ($response->code == 200) {
            $this->output->writeln(json_encode(['Status'=>'Sitemap is accessible','URL'=>$url]));
          } else {
            $this->output->writeln(json_encode(['Status'=>'Sitemap is not accessible','URL'=>$url]));
          }
        } else {
          $this->writeHeader('Sitemap');
          if ($response->code == 200) {
            $this->output->writeln('<info>Sitemap is accessible:</info> ' . $url);
          } else {
            $this->output->writeln('<error>Sitemap is not accessible:</error> ' . $url);
          }
        }

    }

    /**
     * Parse the robots.txt text file to find the sitemap
     *
     * @param bool $json
     *
     * @return string
     */
    protected function getSitemapUrl($json)
    {
        $request = new Request;
        $response = $request->fetch($this->url . 'robots.txt');
        $sitemap = new Sitemap;
        $sitemap->setRequest($this->request);
        $sitemap  = $sitemap->getSitemapFromRobotsTxt($response);

          if ($sitemap === false) {
              if (!$json) {
                $this->output->writeln(
                  '<error>Sitemap is not declared in robots.txt</error>'
                );
              }
              return $this->url . 'sitemap.xml';
          }
          if (!$json) {
            $this->output
              ->writeln('<info>Sitemap is declared in robots.txt</info>');
          }

        return (string) $sitemap;
    }
}
