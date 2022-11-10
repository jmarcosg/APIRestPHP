<?php

include '../../app/config/paths.php';

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

/* Carga del DOTENV - General*/
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

/* Carga del DOTENV - webLogin */
$dotenv = \Dotenv\Dotenv::createImmutable(V1_PATH . 'wlfotoperfil');
$dotenv->load();

define('FILE_PATH', $_ENV['FILE_PATH_LOCAL']);
define('TOKEN', $_ENV['TOKEN_KEY']);

include V1_PATH . 'wlfotoperfil/config.php';
