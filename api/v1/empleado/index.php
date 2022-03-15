<?php

use App\Controllers\EmpleadoController;

$empleadoController = new EmpleadoController();

/* Metodo GET */
if ($token == TOKEN_KEY && $url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$empleado = $empleadoController->getByDocumentoAndGender($_GET);
		if (!$empleado instanceof ErrorException) {
			if ($empleado) {
				sendRes($empleado);
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
			}
		} else {
			sendRes(null, $empleado->getMessage(), $_GET);
		};
	} else {
		header("HTTP/1.1 200 Bad Request");
	}
	eClean();
}

if ($token != TOKEN_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 200 Bad Request");
}
eClean();
