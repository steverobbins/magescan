<?php

include '../vendor/autoload.php';

use MGA\Http;

$code = isset($_GET['code']) ? $_GET['code'] : '';
$url = isset($_GET['url']) ? $_GET['url'] : '';

new Http($code, $url);
