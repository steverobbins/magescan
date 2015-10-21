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

use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan a Magento site using PHP CLI
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class AllCommand extends ScanCommand
{
    /**
     * Input object
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * Output object
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * URL of Magento site
     *
     * @var string
     */
    protected $url;

    /**
     * Cached request object with desired secure flag
     *
     * @var \MageScan\Request
     */
    protected $request;

    /**
     * Configure scan command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan')
            ->setAliases(array('scan:all'))
            ->setDescription('Audit a Magento site as best you can by URL')
            ->addOption(
                'show-modules',
                null,
                InputOption::VALUE_NONE,
                'Show all modules that were scanned for, not just matches'
            );

        parent::configure();
    }

    /**
     * Run scan command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output->writeln('Scanning <info>' . $this->url . '</info>...');

        $this->checkMagentoInfo();
        $this->checkModules();
        $this->checkCatalog();
        $this->checkPatch();
        $this->checkSitemapExists();
        $this->checkServerTech();
        $this->checkUnreachablePath();
    }

    /**
     * Get version information about the Magento application
     *
     * @return void
     */
    protected function checkMagentoInfo()
    {
        $command = $this->getApplication()->find('scan:version');
        $arguments = array(
            'command' => 'scan:version',
            'url'     => $this->url,
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $this->output);
    }

    /**
     * Check for files known to be associated with a module
     *
     * @param boolean $all
     *
     * @return void
     */
    protected function checkModules()
    {
        $command = $this->getApplication()->find('scan:modules');
        $input = new ArrayInput(
            array(
                'command'        => 'scan:modules',
                'url'            => $this->url,
                '--show-modules' => $this->input->getOption('show-modules'),
            )
        );
        $command->run($input, $this->output);
    }

    /**
     * Get catalog data
     *
     * @return void
     */
    protected function checkCatalog()
    {
        $command = $this->getApplication()->find('scan:catalog');
        $input = new ArrayInput(
            array(
                'command' => 'scan:catalog',
                'url'     => $this->url,
            )
        );
        $command->run($input, $this->output);
    }

    /**
     * Check for installed patches
     *
     * @return void
     */
    protected function checkPatch()
    {
        $command = $this->getApplication()->find('scan:patches');
        $input = new ArrayInput(
            array(
                'command' => 'scan:patches',
                'url'     => $this->url,
            )
        );
        $command->run($input, $this->output);
    }

    /**
     * Check HTTP status codes for files/paths that shouldn't be reachable
     *
     * @return void
     */
    protected function checkUnreachablePath()
    {
        $command = $this->getApplication()->find('scan:unreachable');
        $input = new ArrayInput(
            array(
                'command' => 'scan:unreachable',
                'url'     => $this->url,
            )
        );
        $command->run($input, $this->output);
    }

    /**
     * Analyze the server technology being used
     *
     * @return void
     */
    protected function checkServerTech()
    {
        $command = $this->getApplication()->find('scan:server');
        $input = new ArrayInput(
            array(
                'command' => 'scan:server',
                'url'     => $this->url,
            )
        );
        $command->run($input, $this->output);
    }

    /**
     * Check that the store is correctly using a sitemap
     *
     * @return void
     */
    protected function checkSitemapExists()
    {
        $command = $this->getApplication()->find('scan:sitemap');
        $input = new ArrayInput(
            array(
                'command' => 'scan:sitemap',
                'url'     => $this->url,
            )
        );
        $command->run($input, $this->output);
    }

    /**
     * Write a header block
     *
     * @param string $text
     * @param string $style
     *
     * @return void
     */
    protected function writeHeader($text, $style = 'bg=blue;fg=white')
    {
        $this->output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')
                ->formatBlock($text, $style, true),
            '',
        ));
    }
}
