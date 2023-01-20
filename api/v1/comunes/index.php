<?php

use App\Controllers\Common\LoginController;

$dotenv = \Dotenv\Dotenv::createImmutable('./comunes/');
$dotenv->load();

include './comunes/config.php';

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		/* case 'getToken':
			LoginController::getUserByToken(); */

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
