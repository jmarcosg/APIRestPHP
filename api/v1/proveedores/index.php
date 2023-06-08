<?php

use App\Controllers\Proveedor\Pro_SolicitudController;

$dotenv = \Dotenv\Dotenv::createImmutable('./proveedores/');
$dotenv->load();

include './proveedores/config.php';

if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	if (method_exists(Pro_SolicitudController::class, $action)) {
		Pro_SolicitudController::$action();
	} else {
		$error = new ErrorException('El action no es valido');
		sendRes(null, $error->getMessage(), $_POST);
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
