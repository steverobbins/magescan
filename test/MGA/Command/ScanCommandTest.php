<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @author    Steve Robbins <steve@steverobbins.com>
 * @license   http://creativecommons.org/licenses/by/4.0/
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Test\Command;

use MageScan\Command\ScanCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 */
class ScanCommandTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new ScanCommand());

        $command = $application->find('scan');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'        => $command->getName(),
            'url'            => 'demo.magentocommerce.com'
        ));
        $display = <<<DISPLAY
Scanning http://demo.magentocommerce.com/...

                       
  Magento Information  
                       

+-----------+-----------+
| Parameter | Value     |
+-----------+-----------+
| Edition   | Community |
| Version   | 1.9.0.0   |
+-----------+-----------+

                     
  Installed Modules  
                     

No detectable modules were found

                       
  Catalog Information  
                       

+------------+-------+
| Type       | Count |
+------------+-------+
| Categories | 27    |
| Products   | 93    |
+------------+-------+

           
  Patches  
           

+------------+---------+
| Name       | Status  |
+------------+---------+
| SUPEE-5344 | Unknown |
+------------+---------+

           
  Sitemap  
           

Sitemap is not declared in robots.txt
Sitemap is not accessible: http://demo.magentocommerce.com/sitemap.xml

                     
  Server Technology  
                     

+--------------+-------------+
| Key          | Value       |
+--------------+-------------+
| Server       | nginx/1.8.0 |
| X-Powered-By | PHP/5.3.3   |
+--------------+-------------+

                          
  Unreachable Path Check  
                          

+-----------------------+---------------+---------------------------------------------+
| Path                  | Response Code | Status                                      |
+-----------------------+---------------+---------------------------------------------+
| .git/config           | 404           | Pass                                        |
| .svn/entries          | 403           | Pass                                        |
| admin                 | 301           | http://demo-admin.magentocommerce.com/admin |
| app/etc/local.xml     | 403           | Pass                                        |
| composer.json         | 404           | Pass                                        |
| downloader/index.php  | 200           | Fail                                        |
| phpinfo.php           | 404           | Pass                                        |
| phpmyadmin            | 404           | Pass                                        |
| var/log/exception.log | 403           | Pass                                        |
| var/log/system.log    | 403           | Pass                                        |
+-----------------------+---------------+---------------------------------------------+

DISPLAY;
        $this->assertEquals($display, $commandTester->getDisplay());
    }
}
