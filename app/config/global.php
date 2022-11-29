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

/* Modo produccion: true */
define('PROD', $_ENV['PROD'] == 'true' ? true : false);

/* Entorno: local - producción */
define('ENV', $_ENV['ENV']);

/* Token */
define('TOKEN_KEY', $_ENV['TOKEN_KEY']);

define('BASE_WEBLOGIN_APPS', 'https://weblogin.muninqn.gov.ar/apps/');

define('BASE_WEB_LOGIN_API', $_ENV['BASE_WEB_LOGIN_API']);

/* Headers */
include_once 'headers.php';

/* Configuracion de la URL */
include 'url.php';
