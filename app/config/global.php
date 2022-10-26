<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

/* Root Path */
include_once 'paths.php';

/* AutoLoad composer & local */
require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

/* Carga del DOTENV */
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

/* Sentry */
/* \Sentry\init(['dsn' => $_ENV['sentry']]); */

/* Modo produccion: true */
define('PROD', $_ENV['PROD'] == 'true' ? true : false);

/* Entorno: local - producción */
define('ENV', $_ENV['ENV']);

/* Token */
define('TOKEN_KEY', $_ENV['TOKEN_KEY']);

/* ######################### */

if (ENV == 'produccion') {
    define('FILE_PATH', $_ENV['FILE_PATH_PRODUCCION']);
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_PRODUCCION']);
    define('BASE_WEB_LOGIN', $_ENV['BASE_WEB_LOGIN_PRODUCCION']);
}

if (ENV == 'replica') {
    define('FILE_PATH', $_ENV['FILE_PATH_REPLICA']);
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_REPLICA']);
    define('BASE_WEB_LOGIN', $_ENV['BASE_WEB_LOGIN_REPLICA']);
}

if (ENV == 'local') {
    /** LOCAL */
    define('FILE_PATH', 'C:\xampp\htdocs\apirestphp\files\\');
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_PRODUCCION']);
}

if (ENV == 'mac') {
    /** LOCAL */
    define('FILE_PATH', '');
    define('WS_WEBLOGIN', 'http://200.85.183.194:90/api/getUserByToken/');
    define('FILE_PATH', $_ENV['FILE_PATH_LOCAL']);
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_LOCAL']);
    define('BASE_WEB_LOGIN', $_ENV['BASE_WEB_LOGIN_LOCAL']);
}

define('BASE_WEBLOGIN_APPS', 'https://weblogin.muninqn.gov.ar/apps/');

/* Headers */
include_once 'headers.php';

/* Configuracion de la URL */
include 'url.php';
