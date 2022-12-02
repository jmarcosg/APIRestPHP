<?php

/* Configuracion Base de datos */
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);
// define('FILE_PATH', ROOT_PATH . '\\files\\');

define('LOCAL_API_SAVE_PATH', $_ENV['API_LOCAL_SAVE_FILES_URL']);
define('REPLICA_API_SAVE_PATH', ROOT_PATH . '/files');
define('WEBLOGIN_API_SAVE_PATH', ROOT_PATH . '/files');

define('LOCAL_API_FETCH_PATH', $_ENV['API_LOCAL_FETCH_FILES_URL']);
define('REPLICA_API_FETCH_PATH', $_ENV['API_REPLICA_FETCH_FILES_URL']);
define('WEBLOGIN_API_FETCH_PATH', $_ENV['API_WEBLOGIN_FETCH_FILES_URL']);
