<?php

/* Configuracion base de datos */
define('DB_HOST', PROD ? $_ENV['DB_HOST'] : '128.53.15.3');
define('DB_USER',  PROD ? $_ENV['DB_USER'] : 'userturnos');
define('DB_PASS',  PROD ? $_ENV['DB_PASS'] : 'turnero16');
define('DB_NAME',  PROD ? $_ENV['DB_NAME'] : 'infoprueba');
define('DB_PORT',  PROD ? $_ENV['DB_PORT'] : '3306');
define('DB_CHARSET',  PROD ? $_ENV['DB_CHARSET'] : 'utf8');