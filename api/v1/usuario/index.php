<?php

use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

/* Metodo GET */
if ($token == USUARIO_KEY && $url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$usuario = $usuarioController->get($_GET);
		if (!$usuario instanceof ErrorException) {
			if ($usuario) {
				sendRes($usuario);
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
			}
		} else {
			sendRes(null, $usuario->getMessage(), $_GET);
		};
	} else {
		$usuarios = $usuarioController->index(['TOP' => 10]);
		if (!$usuarios instanceof ErrorException) {
			sendRes($usuarios);
		} else {
			sendRes(null, $usuarios->getMessage(), $_GET);
		};
	}
	exit();
}

/* Metodo POST */
if ($token == USUARIO_KEY && $url['method'] == 'POST') {
	$usuario = $usuarioController->store($_POST);
	if (!$usuario instanceof ErrorException) {
		sendRes(['ReferenciaID' => $usuario]);
	} else {
		sendRes(null, $usuario->getMessage(), $_GET);
	};
	exit();
}

/* Metodo PUT */
if ($token == USUARIO_KEY && $url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$usuario = $usuarioController->update($_PUT, $url['id']);
	if (!$usuario instanceof ErrorException) {
		sendRes($_PUT);
	} else {
		sendRes(null, $usuario->getMessage(), $_GET);
	};
	exit();
}

/* Metodo DELETE */
if ($token == USUARIO_KEY && $url['method'] == 'DELETE') {
	$usuario = $usuarioController->delete($url['id']);
	if (!$usuario instanceof ErrorException) {
		sendRes($url['id']);
	} else {
		sendRes(null, $usuario->getMessage(), $url['id']);
	};
	exit();
}

if ($token != USUARIO_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 200 Bad Request");
}
exit();
