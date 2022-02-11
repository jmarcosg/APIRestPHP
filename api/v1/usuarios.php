<?php

include '../../app/config/global.php';

use App\Controllers\UsuarioController;

$token = getBearerToken();

if ($token == USUARIO_KEY) {
	if ($rm == 'GET') {
		if (isset($_GET) && count($_GET) > 0) {
			$usuarioController = new UsuarioController();
			$usuario = $usuarioController->get($_GET);
			if (!$usuario instanceof ErrorException) {
				sendRes($usuario);
				exit();
			} else {
				sendRes(null, $usuario->getMessage(), $_GET);
				exit();
			};
			sendRes(null, 'no se encontro el usuario', $_GET);
			exit();
		} else {
			$usuarioController = new UsuarioController();
			$usuarios = $usuarioController->index();
			sendRes($usuarios);
			header("HTTP/1.1 200 OK");
		}
	}

	// Crear un nuevo post
	if ($rm == 'POST') {
		header("HTTP/1.1 200 OK");
		exit();
	}

	//Borrar
	if ($rm == 'DELETE') {
		header("HTTP/1.1 200 OK");
		exit();
	}

	//Actualizar
	if ($rm == 'PUT') {
		header("HTTP/1.1 200 OK");
		exit();
	}

	header("HTTP/1.1 400 Bad Request");
} else {
	sendRes(null, 'Token de seguridad erroneo', null);
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
