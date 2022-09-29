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
    /** PRODUCCION */
    define('FILE_PATH', 'E:\Dataserver\Produccion\projects_files\\');
    define('WS_WEBLOGIN', 'https://weblogin.muninqn.gov.ar/api/getUserByToken/');
}

if (ENV == 'replica') {
    /** REPLICA */
    define('FILE_PATH', 'E:\Dataserver\Replica\projects_files\\');
    define('WS_WEBLOGIN', 'http://200.85.183.194:90/api/getUserByToken/');
}

if (ENV == 'local') {
    /** LOCAL */
    define('FILE_PATH', 'C:\xampp\htdocs\APIRest\files\\');
    define('WS_WEBLOGIN', 'https://weblogin.muninqn.gov.ar/api/getUserByToken/');
}

/* Headers */
include_once 'headers.php';

/* Database */
include 'db.php';

/* Configuracion de la URL */
if (!isset($noUrl)) {
    include 'url.php';
}
