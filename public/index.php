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

require_once '../vendor/autoload.php';

use MageScan\Url;
use MageScan\Request;

$suggestUrl = '';
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $magescanUrl = new Url;
    $url = $magescanUrl->clean(urldecode($_GET['url']));
    $request = new Request;
    $response = $request->fetch($url, array(
        CURLOPT_NOBODY => true
    ));
    if (isset($response->header['Location'])) {
        $suggestUrl = $response->header['Location'];
    }
    if (isset($response->header['location'])) {
        $suggestUrl = $response->header['location'];
    }
    $suggestUrl = trim($suggestUrl, '/');
} else {
    $url = false;
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mage Scan<?php echo $url ? ' - ' .$url : '' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/loaders.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-inverse">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="/">Mage Scan <?php echo file_get_contents('download/version') ?></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="https://github.com/steverobbins/magescan">GitHub</a></li>
                    <li><a href="download/magescan.phar">Download <strong>magescan.phar</strong></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="page-header">
            <h1>Scan Your Magento Store</h1>
        </div>
        <form id="magescan-form" method="get" action="/">
            <fieldset>
                <input type="text" placeholder="http://store.example.com/" autofocus="autofocus" name="url"<?php echo $url ? ' value="' . $url . '"' : '' ?> />
                <input type="submit" value="Scan" />
            </fieldset>
        </form>
        <?php if ($suggestUrl && $url != $suggestUrl): ?>
        <div class="suggest">
            Did you mean <a href="?url=<?php echo urlencode($suggestUrl) ?>"><?php echo $suggestUrl ?></a>?
        </div>
        <?php endif ?>
    </div>
    <?php if ($url): ?>
    <div class="container">
        <div class="page-header">
            <h2>Results for <a href="<?php echo $url ?>"><?php echo $url ?></a></h2>
        </div>
        <div id="results">
            <div class="row">
                <div class="col-sm-4" id="magentoinfo">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Magento</h3>
                        </div>
                        <div class="panel-body response">
                            <div class="loader">
                                <div class="loader-inner ball-clip-rotate-multiple">
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" id="sitemap">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Sitemap</h3>
                        </div>
                        <div class="panel-body response">
                            <div class="loader">
                                <div class="loader-inner ball-clip-rotate-multiple">
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" id="catalog">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Catalog <span class="label label-info">?</span></h3>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-info">This only includes visible and enabled entities for this store view.</div>
                            <div class="response">
                                <div class="loader">
                                    <div class="loader-inner ball-clip-rotate-multiple">
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-default" id="servertech">
                        <div class="panel-heading">
                            <h3 class="panel-title">Technology</h3>
                        </div>
                        <div class="panel-body response">
                            <div class="loader">
                                <div class="loader-inner ball-clip-rotate-multiple">
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" id="patch">
                        <div class="panel-heading">
                            <h3 class="panel-title">Patches</h3>
                        </div>
                        <div class="panel-body response">
                            <div class="loader">
                                <div class="loader-inner ball-clip-rotate-multiple">
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" id="modules">
                        <div class="panel-heading">
                            <h3 class="panel-title">Modules</h3>
                        </div>
                        <div class="panel-body response">
                            <div class="loader">
                                <div class="loader-inner ball-clip-rotate-multiple">
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8" id="unreachablepath">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Sensitive URLs <span class="label label-info">?</span></h3>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-info">These are URLs that may reveal sensative information about the system.  They shouldn't be visible to the public.</div>
                            <div class="response">
                                <div class="loader">
                                    <div class="loader-inner ball-clip-rotate-multiple">
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        jQuery(document).ready(function() {
            MageScan.scan('<?php echo $url ?>');  
        })
    </script>
    <?php endif ?>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-16126282-21', 'auto');
        ga('send', 'pageview');
    </script>
</body>
</html>
