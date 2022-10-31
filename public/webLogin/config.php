<?php

include '../../app/config/paths.php';

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

/* Carga del DOTENV - General*/
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

/* Carga del DOTENV - Weblogin */
$dotenv = \Dotenv\Dotenv::createImmutable("./");
$dotenv->load();

define('WEBLOGIN2', 'https://weblogin.muninqn.gov.ar/api/webLogin2');

/* Modo produccion: true */
define('PROD', $_ENV['PROD'] == 'true' ? true : false);

/* Entorno: local - producción */
define('ENV', $_ENV['ENV']);

define('FETCH_LEGAJO', $_ENV['FETCH_LEGAJO'] == 'true' ? true : false);
define('FETCH_LIBRETA', $_ENV['FETCH_LIBRETA'] == 'true' ? true : false);
define('FETCH_LICENCIA', $_ENV['FETCH_LICENCIA'] == 'true' ? true : false);
define('FETCH_ACARREO', $_ENV['FETCH_ACARREO'] == 'true' ? true : false);
define('FETCH_MUNI_EVENTOS', $_ENV['FETCH_MUNI_EVENTOS'] == 'true' ? true : false);

if (ENV == 'produccion') {
    define('BASE_WEB_LOGIN', $_ENV['BASE_WEB_LOGIN_PRODUCCION']);
    define('URL_MUNI_EVENTOS', $_ENV['URL_MUNI_EVENTOS_PRODUCCION']);
}

if (ENV == 'replica') {
    define('BASE_WEB_LOGIN', $_ENV['BASE_WEB_LOGIN_REPLICA']);
    define('URL_MUNI_EVENTOS', $_ENV['URL_MUNI_EVENTOS_REPLICA']);
}

if (ENV == 'local') {
    define('BASE_WEB_LOGIN', $_ENV['BASE_WEB_LOGIN_LOCAL']);
    define('URL_MUNI_EVENTOS', $_ENV['URL_MUNI_EVENTOS_REPLICA']);
}

/* Token */
define('TOKEN_KEY', $_ENV['TOKEN_KEY']);

/* Configuracion Base de datos */
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);
