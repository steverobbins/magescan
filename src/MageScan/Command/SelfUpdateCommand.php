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

namespace MageScan\Command;

use MageScan\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update magescan to latest version
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class SelfUpdateCommand extends Command
{
    /**
     * Configure selfupdate command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('selfupdate')
            ->setDescription('Updates magescan.phar to the latest version')
            ->setHelp(<<<EOT
The <info>selfupdate</info> command checks the homepage for newer
versions of magescan and if found, installs the latest.

<info>php magescan.phar selfupdate</info>
EOT
            )
        ;
    }

    /**
     * Run selfupdate command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $localFilename = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        $tempFilename = dirname($localFilename) . '/' . basename($localFilename, '.phar').'-temp.phar';

        // check for permissions in local filesystem before start connection process
        if (!is_writable($tempDirectory = dirname($tempFilename))) {
            throw new Exception('magescan update failed: the "' . $tempDirectory . '" directory used to download the temp file could not be written');
        }

        if (!is_writable($localFilename)) {
            throw new Exception('magescan update failed: the "' . $localFilename . '" file could not be written');
        }

        $latest = $this->checkLatestVersion();
        if ($this->getApplication()->getVersion() !== $latest) {
            $output->writeln(sprintf("Updating to version <info>%s</info>.", $latest));

            $this->downloadLatestVersion($tempFilename);

            if (!file_exists($tempFilename)) {
                $output->writeln('<error>The download of the new magescan version failed for an unexpected reason');

                return 1;
            }

            try {
                \error_reporting(E_ALL);

                @chmod($tempFilename, 0777 & ~umask());
                // test the phar validity
                $phar = new \Phar($tempFilename);
                // free the variable to unlock the file
                unset($phar);
                @rename($tempFilename, $localFilename);
                $output->writeln('<info>Successfully updated magescan</info>');

                $this->_exit();
            } catch (\Exception $e) {
                @unlink($tempFilename);
                if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
                    throw $e;
                }
                $output->writeln('<error>The download is corrupted ('.$e->getMessage().').</error>');
                $output->writeln('<error>Please re-run the selfupdate command to try again.</error>');
            }
        } else {
            $output->writeln("<info>You are using the latest magescan version.</info>");
        }
    }

    /**
     * Fetch version number of latest release from homepage
     *
     * @throws Exception
     */
    protected function checkLatestVersion()
    {
        $request = new Request;
        $latestURL = 'http://magescan.project.steverobbins.name/download/magescan-version';
        $latestResponse = $request->fetch($latestURL);
        if ($latestResponse->code !== 200) {
            throw new \Exception('Error fetching latest version');
        }

        return $latestResponse->body;
    }

    /**
     * Download the latest version of magescan to temp location
     *
     * @param $tempFilename
     *
     * @return void
     */
    protected function downloadLatestVersion($tempFilename)
    {
        $request = new Request;
        $remoteFilename = 'http://magescan.project.steverobbins.name/download/magescan.phar';
        $fileContents = $request->fetch($remoteFilename);
        file_put_contents($tempFilename, $fileContents->body);
    }

    /**
     * Stop execution
     *
     * This is a workaround to prevent warning of dispatcher after replacing
     * the phar file.
     *
     * @return void
     */
    protected function _exit()
    {
        exit;
    }
}
