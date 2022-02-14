<?php

include '../../../app/config/global.php';

use App\Controllers\UsuarioController;

$token = getBearerToken();

$usuarioController = new UsuarioController();

/* Metodo GET */
if ($token == USUARIO_KEY && $rm == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$usuario = $usuarioController->get($_GET);
		if (!$usuario instanceof ErrorException) {
			if ($usuario) {
				sendRes($usuario);
				header("HTTP/1.1 200 OK");
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
				header("HTTP/1.1 403 Not Found");
			}
		} else {
			sendRes(null, $usuario->getMessage(), $_GET);
			header("HTTP/1.1 403 Not Found");
		};
	} else {
		$usuarios = $usuarioController->index(['TOP' => 10]);
		if (!$usuarios instanceof ErrorException) {
			sendRes($usuarios);
			header("HTTP/1.1 201 OK");
		} else {
			sendRes(null, $usuarios->getMessage(), $_GET);
			header("HTTP/1.1 400 Error");
		};
	}

	exit();
}

/* Metodo POST */
if ($token == USUARIO_KEY &&  $rm == 'POST') {
	$usuario = $usuarioController->store($_POST);
	sendRes(['ReferenciaID' => $usuario]);
	header("HTTP/1.1 200 OK");
	exit();
}

/* Metodo DELETE */
if ($token == USUARIO_KEY &&  $rm == 'DELETE') {
	header("HTTP/1.1 200 OK");
	exit();
}

/* Metodo PUT */
if ($token == USUARIO_KEY && $rm == 'PUT') {
	header("HTTP/1.1 200 OK");
	exit();
}

if ($token != USUARIO_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 400 Bad Request");
}
exit();
