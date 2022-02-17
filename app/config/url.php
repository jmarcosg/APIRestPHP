<?php

if (ENV == 'local') {
    $indexPath = 5;
    $indexId = 6;
} else {
    $indexPath = 6;
    $indexId = 7;
}

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', $url);

$url = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => strtolower($url[$indexPath]),
    'id' => isset($url[$indexId]) ? $url[$indexId] : null,
    'url' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
];
