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

/* Token */
define('TOKEN_KEY', $_ENV['TOKEN_KEY']);

include '../../app/config/db.php';
