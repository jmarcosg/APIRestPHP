<?php

/* Root Path */
include_once 'paths.php';

/* AutoLoad composer & local */
require '../../vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

/* Carga del DOTENV */
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

/* ######################### */

/* Headers */
include_once 'headers.php';

/* Tokens */
include 'tokens.php';

$rm = $_SERVER['REQUEST_METHOD'];
