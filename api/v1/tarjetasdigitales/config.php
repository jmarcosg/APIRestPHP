<?php

/* Configuracion Base de datos */
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);
define('FILE_PATH', $_ENV['ENV'] == 'replica' ? "E:\Dataserver\Replica\projects_files\qr-identificacion\\" : "E:\Dataserver\Produccion\projects_files\qr-identificacion\\");
define('ENV', $_ENV['ENV']);
