<?php

include '../../app/config/paths.php';

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

/* Carga del DOTENV */
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();


/* Modo produccion: true */
define('PROD', $_ENV['PROD'] == 'true' ? true : false);

if (PROD) {
    # code...
    define('WEBLOGIN2', 'http://localhost/api/webLogin2');
} else {
    define('WEBLOGIN2', 'https://weblogin.muninqn.gov.ar/api/webLogin2');
}
/* Entorno: local - producción */
define('ENV', $_ENV['ENV']);

/* Token */
define('TOKEN_KEY', $_ENV['TOKEN_KEY']);

include '../../app/config/db.php';
