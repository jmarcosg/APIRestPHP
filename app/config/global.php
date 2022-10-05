<?php
$GLOBALS[] = [
    'exect' => []
];

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
}

if (ENV == 'replica') {
    define('FILE_PATH', $_ENV['FILE_PATH_REPLICA']);
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_REPLICA']);
}

if (ENV == 'local') {
    define('FILE_PATH', $_ENV['FILE_PATH_LOCAL']);
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_LOCAL']);
}

if (ENV == 'mac') {
    /** LOCAL */
    define('FILE_PATH', '');
    define('WS_WEBLOGIN', $_ENV['WS_WEBLOGIN_LOCAL']);
}

/* Headers */
include_once 'headers.php';

/* Database */
include 'db.php';

/* Configuracion de la URL */
if (!isset($noUrl)) {
    include 'url.php';
}
