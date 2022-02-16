<?php

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', $url);

$url = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => strtolower($url[6]),
    'id' => isset($url[7]) ? $url[7] : null
];
