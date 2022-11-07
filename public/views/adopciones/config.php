<?php

include "../../../app/config/paths.php";

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);

if ($_ENV['ENV'] == 'replica') {
    define('FILE_PATH', "E:\Dataserver\Replica\projects_files\qr-identificacion\\");
} else if ($_ENV['ENV'] == 'produccion') {
    define('FILE_PATH', "E:\Dataserver\Produccion\projects_files\qr-identificacion\\");
} else if ($_ENV['ENV'] == 'local') {
    define('FILE_PATH', "C:\\xampp\\htdocs\\apirestphp\\files\\");
}

define('ENV', $_ENV['ENV']);
