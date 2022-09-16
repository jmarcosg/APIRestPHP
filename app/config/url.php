<?php

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', $url);
unset($url[0]);

/* Verifiacmos si ingreso una '/' al final */
if (end($url) == "") {
    unset($url[array_search(end($url), $url)]);
}

$lastElement = end($url);
$id = null;
if (is_numeric($lastElement)) {
    /* Si el ultimo campo es un numero, asumimos que es un identificador */
    $id = intval(end($url));

    /* Lo borramos el arreglo para obtener el resto de los elementos */
    unset($url[array_search(end($url), $url)]);

    /* Obtenemos el ultimo elemento nuevamente */
    $lastElement = end($url);
}

$subPaths = [];
while ($lastElement != 'index.php') {
    $index = array_search(end($url), $url);
    if ($url[$index - 1] == 'index.php') {
        $path = strtolower($url[$index]);
    } else {
        $subPaths[] = strtolower($url[$index]);
    }
    unset($url[$index]);
    $lastElement = end($url);
}

$url = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => $path,
    'subPaths' => $subPaths,
    'id' => $id,
    'url' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
];
