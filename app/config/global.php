<?php

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

/* ######################### */
define('WEBLOGIN2', PROD ? 'localhost/api/webLogin2' : 'https://weblogin.muninqn.gov.ar/api/webLogin2');

/* Headers */
include_once 'headers.php';

/* Tokens */
include 'tokens.php';

/* Database */
include 'db.php';

/* Configuracion de la URL */
include 'url.php';
