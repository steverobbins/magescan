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

use MageScan\Command\Scan\AllCommand;
use MageScan\Command\Scan\CatalogCommand;
use MageScan\Command\Scan\ModuleCommand;
use MageScan\Command\Scan\PatchCommand;
use MageScan\Command\Scan\ServerCommand;
use MageScan\Command\Scan\SitemapCommand;
use MageScan\Command\Scan\VersionCommand;
use MageScan\Command\Scan\UnreachableCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_TestCase;

/**
 * Run tests on the scan command
 */
class ScanAllCommandTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application;
        $application->add(new AllCommand);
        $application->add(new CatalogCommand);
        $application->add(new ModuleCommand);
        $application->add(new PatchCommand);
        $application->add(new ServerCommand);
        $application->add(new SitemapCommand);
        $application->add(new VersionCommand);
        $application->add(new UnreachableCommand);

        $command = $application->find('scan:all');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'        => 'scan:all',
            'url'            => 'enterprise-demo.user.magentotrial.com'
        ));
        $display = <<<DISPLAY
Scanning http://enterprise-demo.user.magentotrial.com/...

                       
  Magento Information  
                       

+-----------+------------+
| Parameter | Value      |
+-----------+------------+
| Edition   | Enterprise |
| Version   | 1.14.1.0   |
+-----------+------------+

                     
  Installed Modules  
                     

No detectable modules were found

                       
  Catalog Information  
                       

+------------+-------+
| Type       | Count |
+------------+-------+
| Categories | 28    |
| Products   | 94    |
+------------+-------+

           
  Patches  
           

+------------+-----------+
| Name       | Status    |
+------------+-----------+
| SUPEE-5344 | Patched   |
| SUPEE-5994 | Unpatched |
| SUPEE-6285 | Unpatched |
| SUPEE-6482 | Unpatched |
+------------+-----------+

           
  Sitemap  
           

Sitemap is not declared in robots.txt
Sitemap is not accessible: http://enterprise-demo.user.magentotrial.com/sitemap.xml

                     
  Server Technology  
                     

+--------+-------+
| Key    | Value |
+--------+-------+
| Server | nginx |
+--------+-------+

                          
  Unreachable Path Check  
                          

+----------------------------------------------+---------------+-----------------------------------------------------+
| Path                                         | Response Code | Status                                              |
+----------------------------------------------+---------------+-----------------------------------------------------+
| .bzr/                                        | 404           | Pass                                                |
| .cvs/                                        | 404           | Pass                                                |
| .git/                                        | 403           | Pass                                                |
| .git/config                                  | 403           | Pass                                                |
| .git/refs/                                   | 403           | Pass                                                |
| .gitignore                                   | 404           | Pass                                                |
| .hg/                                         | 404           | Pass                                                |
| .idea                                        | 404           | Pass                                                |
| .svn/                                        | 403           | Pass                                                |
| .svn/entries                                 | 403           | Pass                                                |
| admin/                                       | 301           | http://enterprise-admin.user.magentotrial.com/admin |
| admin123/                                    | 301           | http://enterprise-admin.user.magentotrial.com/admin |
| adminer.php                                  | 301           | http://enterprise-admin.user.magentotrial.com/admin |
| administrator/                               | 301           | http://enterprise-admin.user.magentotrial.com/admin |
| adminpanel/                                  | 301           | http://enterprise-admin.user.magentotrial.com/admin |
| aittmp/index.php                             | 404           | Pass                                                |
| app/etc/enterprise.xml                       | 403           | Pass                                                |
| app/etc/local.xml                            | 403           | Pass                                                |
| backend/                                     | 404           | Pass                                                |
| backoffice/                                  | 404           | Pass                                                |
| beheer/                                      | 404           | Pass                                                |
| chive                                        | 404           | Pass                                                |
| composer.json                                | 404           | Pass                                                |
| composer.lock                                | 404           | Pass                                                |
| control/                                     | 200           | Fail                                                |
| dev/tests/functional/etc/config.xml          | 404           | Pass                                                |
| downloader/index.php                         | 200           | Fail                                                |
| index.php/rss/order/NEW/new                  | 200           | Fail                                                |
| info.php                                     | 404           | Pass                                                |
| mageaudit.php                                | 404           | Pass                                                |
| magmi/                                       | 404           | Pass                                                |
| magmi/conf/magmi.ini                         | 404           | Pass                                                |
| magmi/web/magmi.php                          | 404           | Pass                                                |
| manage/                                      | 404           | Pass                                                |
| management/                                  | 404           | Pass                                                |
| manager/                                     | 404           | Pass                                                |
| p.php                                        | 404           | Pass                                                |
| panel/                                       | 404           | Pass                                                |
| phpinfo.php                                  | 404           | Pass                                                |
| phpmyadmin                                   | 404           | Pass                                                |
| README.md                                    | 404           | Pass                                                |
| README.txt                                   | 404           | Pass                                                |
| shell/                                       | 403           | Pass                                                |
| shopadmin/                                   | 404           | Pass                                                |
| site_admin/                                  | 404           | Pass                                                |
| var/export/                                  | 403           | Pass                                                |
| var/export/export_all_products.csv           | 403           | Pass                                                |
| var/export/export_customers.csv              | 403           | Pass                                                |
| var/export/export_product_stocks.csv         | 403           | Pass                                                |
| var/log/                                     | 403           | Pass                                                |
| var/log/exception.log                        | 403           | Pass                                                |
| var/log/payment_authnetcim.log               | 403           | Pass                                                |
| var/log/payment_authorizenet.log             | 403           | Pass                                                |
| var/log/payment_authorizenet_directpost.log  | 403           | Pass                                                |
| var/log/payment_cybersource_soap.log         | 403           | Pass                                                |
| var/log/payment_ogone.log                    | 403           | Pass                                                |
| var/log/payment_payflow_advanced.log         | 403           | Pass                                                |
| var/log/payment_payflow_link.log             | 403           | Pass                                                |
| var/log/payment_paypal_billing_agreement.log | 403           | Pass                                                |
| var/log/payment_paypal_direct.log            | 403           | Pass                                                |
| var/log/payment_paypal_express.log           | 403           | Pass                                                |
| var/log/payment_paypal_standard.log          | 403           | Pass                                                |
| var/log/payment_paypaluk_express.log         | 403           | Pass                                                |
| var/log/payment_pbridge.log                  | 403           | Pass                                                |
| var/log/payment_verisign.log                 | 403           | Pass                                                |
| var/log/system.log                           | 403           | Pass                                                |
| var/report/                                  | 403           | Pass                                                |
+----------------------------------------------+---------------+-----------------------------------------------------+

DISPLAY;
        $this->assertEquals($display, $commandTester->getDisplay());
    }
}
