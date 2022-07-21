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
define('WEBLOGIN2', PROD ? 'localhost/api/webLogin2' : 'https://weblogin.muninqn.gov.ar/api/webLogin2');

if (ENV == 'produccion') {
    define('FILE_PATH', 'E:\Dataserver\Produccion\projects_files\\');
}

if (ENV == 'replica') {
    define('FILE_PATH', 'E:\Dataserver\Replica\projects_files\\');
}

if (ENV == 'local') {
    define('FILE_PATH', 'http://localhost/APIrest/files/');
}


/* Headers */
include_once 'headers.php';

/* Database */
include 'db.php';

/* Configuracion de la URL */
include 'url.php';
