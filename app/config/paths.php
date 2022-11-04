<?php

define('ROOT_PATH', substr(__DIR__, 0, strlen(__DIR__) - 10));
define('PUBLIC_PATH', ROOT_PATH . 'public');
/* define('FILE_PATH_LOCAL', ROOT_PATH . 'files/'); */
define('V1_PATH', ROOT_PATH . 'api/v1/');

/* Necesitamos definir correctamente esta ruta */
define('FILE_PATH_PROD', ROOT_PATH . 'files/');

define('VIEW_PATH', ROOT_PATH . 'public/views');
define('APP_PATH', ROOT_PATH . 'app');
define('CON_PATH', ROOT_PATH . 'app/connections');
define('UTIL_PATH', ROOT_PATH . 'app/utils');
define('LOG_PATH', ROOT_PATH . 'logs/');
define('TEM_PATH', ROOT_PATH . 'temp/');

/* Path donde se guardan los archivos para poder hacer envio de email adjuntando dichos archivos */
define('ADJUNTOS_PATH', "E:\Dataserver\Produccion\Adjuntos\\");
