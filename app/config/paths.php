<?php

define('ROOT_PATH', substr(__DIR__, 0, strlen(__DIR__) - 10));
define('PUBLIC_PATH', ROOT_PATH . 'public');
define('FILE_PATH_LOCAL', ROOT_PATH . 'files/');

/* Necesitamos definir correctamente esta ruta */
define('FILE_PATH_PROD', ROOT_PATH . 'files/');

define('VIEW_PATH', ROOT_PATH . 'public/views');
define('APP_PATH', ROOT_PATH . 'app');
define('CON_PATH', ROOT_PATH . 'app/connections');
define('UTIL_PATH', ROOT_PATH . 'app/utils');
define('LOG_PATH', ROOT_PATH . 'logs/');
