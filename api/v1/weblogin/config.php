<?php

/* Configuracion Base de datos */
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);

if (ENV == 'produccion') {
    define('URL_MUNI_EVENTOS', $_ENV['URL_MUNI_EVENTOS_PRODUCCION']);
}

if (ENV == 'replica') {
    define('URL_MUNI_EVENTOS', $_ENV['URL_MUNI_EVENTOS_REPLICA']);
}

if (ENV == 'local') {
    define('URL_MUNI_EVENTOS', $_ENV['URL_MUNI_EVENTOS_REPLICA']);
}
