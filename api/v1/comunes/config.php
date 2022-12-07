<?php

/* Configuracion Base de datos */

if (!isset($_GET['entorno'])) {
    sendRes(null, 'Require enviar un entorno', $_GET);
}

if ($_GET['entorno'] != 'produccion' && $_GET['entorno'] != 'replica') {
    sendRes(null, 'Entorno invalido', $_GET);
}

if ($_GET['entorno'] == 'produccion') {
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
    define('DB_NAME', $_ENV['DB_NAME']);
    define('DB_PORT', $_ENV['DB_PORT']);
    define('DB_CHARSET', $_ENV['DB_CHARSET']);
}

if ($_GET['entorno'] == 'replica') {    
    define('DB_HOST', $_ENV['DB_HOST_R']);
    define('DB_USER', $_ENV['DB_USER_R']);
    define('DB_PASS', $_ENV['DB_PASS_R']);
    define('DB_NAME', $_ENV['DB_NAME_R']);
    define('DB_PORT', $_ENV['DB_PORT_R']);
    define('DB_CHARSET', $_ENV['DB_CHARSET_R']);
}
