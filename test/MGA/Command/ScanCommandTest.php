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
                          

+----------------------------------------------+---------------+---------------------------------------------+
| Path                                         | Response Code | Status                                      |
+----------------------------------------------+---------------+---------------------------------------------+
| .bzr/                                        | 404           | Pass                                        |
| .cvs/                                        | 404           | Pass                                        |
| .git/                                        | 404           | Pass                                        |
| .git/config                                  | 404           | Pass                                        |
| .git/refs/                                   | 404           | Pass                                        |
| .gitignore                                   | 404           | Pass                                        |
| .hg/                                         | 404           | Pass                                        |
| .idea                                        | 404           | Pass                                        |
| .svn/                                        | 403           | Pass                                        |
| .svn/entries                                 | 403           | Pass                                        |
| admin                                        | 301           | http://demo-admin.magentocommerce.com/admin |
| adminer.php                                  | 301           | http://demo-admin.magentocommerce.com/admin |
| app/etc/enterprise.xml                       | 403           | Pass                                        |
| app/etc/local.xml                            | 403           | Pass                                        |
| chive                                        | 404           | Pass                                        |
| composer.json                                | 404           | Pass                                        |
| composer.lock                                | 404           | Pass                                        |
| dev/tests/functional/etc/config.xml          | 404           | Pass                                        |
| downloader/index.php                         | 200           | Fail                                        |
| info.php                                     | 404           | Pass                                        |
| magmi/                                       | 404           | Pass                                        |
| magmi/conf/magmi.ini                         | 404           | Pass                                        |
| magmi/web/magmi.php                          | 404           | Pass                                        |
| p.php                                        | 404           | Pass                                        |
| phpinfo.php                                  | 404           | Pass                                        |
| phpmyadmin                                   | 404           | Pass                                        |
| README.md                                    | 404           | Pass                                        |
| README.txt                                   | 404           | Pass                                        |
| shell/                                       | 404           | Pass                                        |
| var/export/                                  | 403           | Pass                                        |
| var/export/export_all_products.csv           | 403           | Pass                                        |
| var/export/export_customers.csv              | 403           | Pass                                        |
| var/export/export_product_stocks.csv         | 403           | Pass                                        |
| var/log/                                     | 403           | Pass                                        |
| var/log/exception.log                        | 403           | Pass                                        |
| var/log/payment_authnetcim.log               | 403           | Pass                                        |
| var/log/payment_authorizenet.log             | 403           | Pass                                        |
| var/log/payment_authorizenet_directpost.log  | 403           | Pass                                        |
| var/log/payment_cybersource_soap.log         | 403           | Pass                                        |
| var/log/payment_ogone.log                    | 403           | Pass                                        |
| var/log/payment_payflow_advanced.log         | 403           | Pass                                        |
| var/log/payment_payflow_link.log             | 403           | Pass                                        |
| var/log/payment_paypal_billing_agreement.log | 403           | Pass                                        |
| var/log/payment_paypal_direct.log            | 403           | Pass                                        |
| var/log/payment_paypal_express.log           | 403           | Pass                                        |
| var/log/payment_paypal_standard.log          | 403           | Pass                                        |
| var/log/payment_paypaluk_express.log         | 403           | Pass                                        |
| var/log/payment_pbridge.log                  | 403           | Pass                                        |
| var/log/payment_verisign.log                 | 403           | Pass                                        |
| var/log/system.log                           | 403           | Pass                                        |
| var/report/                                  | 403           | Pass                                        |
+----------------------------------------------+---------------+---------------------------------------------+

DISPLAY;
        $this->assertEquals($display, $commandTester->getDisplay());
    }
}
