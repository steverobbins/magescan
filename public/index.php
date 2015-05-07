<?php

include '../vendor/autoload.php';

use MGA\Url;
use MGA\Request;

if (isset($_GET['url'])) {
    $mgaUrl = new Url;
    $url = $mgaUrl->clean(urldecode($_GET['url']));
    $request = new Request;
    $response = $request->fetch($url, array(
        CURLOPT_NOBODY => true
    ));
    if (isset($response->header['Location'])) {
        $url = $response->header['Location'];
    }
    if (isset($response->header['location'])) {
        $url = $response->header['location'];
    }
} else {
    $url = false;
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Magento Guest Audit</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
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
                <a class="navbar-brand" href="/">Magento Guest Audit</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="https://github.com/steverobbins/magento-guest-audit">GitHub</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="page-header">
            <h1>Start a New Scan</h1>
        </div>
        <form id="mga-form" method="get" action="/">
            <fieldset>
                <input type="text" placeholder="Magento URL" name="url"<?php echo $url ? ' value="' . $url . '"' : '' ?> />
                <input type="submit" value="Scan" />
            </fieldset>
        </form>
    </div>
    <?php if ($url): ?>
    <div class="container">
        <div class="page-header">
            <h1>Results for <a href="<?php echo $url ?>"><?php echo $url ?></a></h1>
        </div>
        <div id="results">
            <div class="row">
                <div class="col-sm-4" id="magentoinfo">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Magento Information</h3>
                        </div>
                        <div class="panel-body response">Scanning...</div>
                    </div>
                </div>
                <div class="col-sm-4" id="sitemap">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Sitemap</h3>
                        </div>
                        <div class="panel-body response">Scanning...</div>
                    </div>
                </div>
                <div class="col-sm-4" id="servertech">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Server Technology</h3>
                        </div>
                        <div class="panel-body response">Scanning...</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4" id="catalog">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Catalog Information</h3>
                        </div>
                        <div class="panel-body response">Scanning...</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" id="modules">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Installed Modules</h3>
                        </div>
                        <div class="panel-body response">Scanning...</div>
                    </div>
                </div>
                <div class="col-sm-6" id="unreachablepath">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Unreachable Path Check</h3>
                        </div>
                        <div class="panel-body response">Scanning...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        jQuery(document).ready(function() {
            MGA.scan('<?php echo $url ?>');  
        })
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <?php endif ?>
</body>
</html>