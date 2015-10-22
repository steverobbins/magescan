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

namespace MageScan\Command;

use MageScan\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update magescan to latest version
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class SelfUpdateCommand extends Command
{
    const URL_VERSION  = 'http://magescan.steverobbins.com/download/version';
    const URL_DOWNLOAD = 'http://magescan.steverobbins.com/download/magescan.phar';

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
            );
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
        $localFilename = $this->getCurrentFile();
        $tempFilename  = $this->getTempFile($localFilename);
        $version       = $this->getApplication()->getVersion();
        $latest        = $this->checkLatestVersion();
        if (version_compare($version, $latest) >= 0) {
            return $output->writeln(sprintf(
                'You are using the latest version <info>%s</info>',
                $version
            ));
        }
        $output->writeln(sprintf(
            'Updating from <info>%s</info> to <info>%s</info>',
            $version,
            $latest
        ));
        if (!$this->downloadLatestVersion($tempFilename)) {
            return $output->writeln(
                '<error>The download failed unexpectedly</error>'
            );
        }
        $test = $this->testPharValidity($tempFilename);
        if ($test !== true) {
            unlink($tempFilename);
            return $output->writeln('<error>Update failed</error> ' . $test);
        }
        chmod($tempFilename, 0777 & ~umask());
        rename($tempFilename, $localFilename);
        $output->writeln('<info>Mage Scan successfully updated</info>');
    }

    /**
     * Gets the path/name of the local executable file
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getCurrentFile()
    {
        $file = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        if (!is_writable($file)) {
            throw new Exception(
                'Update failed: "' . $file . '" is not writable'
            );
        }
        return $file;
    }

    /**
     * Gets the path/name of the temporary download file
     *
     * @param string $currentFile
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getTempFile($currentFile)
    {
        $file = dirname($currentFile) . DIRECTORY_SEPARATOR
            . basename($currentFile, '.phar') . '-temp.phar';
        if (!is_writable($dir = dirname($file))) {
            throw new Exception(
                'Update failed: "' . $dir . '" is not writable'
            );
        }
        return $file;
    }

    /**
     * Test that the downloaded phar is valid
     *
     * @param string $file
     *
     * @return boolean|string
     */
    protected function testPharValidity($file)
    {
        try {
            new \Phar($file);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * Fetch version number of latest release
     *
     * @return string
     *
     * @throws Exception
     */
    protected function checkLatestVersion()
    {
        $request  = new Request;
        $response = $request->fetch(self::URL_VERSION);
        if ($response->code !== 200) {
            throw new \Exception('Error fetching latest version');
        }
        return trim($response->body);
    }

    /**
     * Download the latest version of magescan
     *
     * @param string $filename
     *
     * @return void
     */
    protected function downloadLatestVersion($filename)
    {
        $request  = new Request;
        $response = $request->fetch(self::URL_DOWNLOAD);
        return file_put_contents($filename, $response->body) !== false;
    }
}
