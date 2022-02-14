<?php

include '../../../app/config/global.php';

use App\Controllers\UsuarioController;

$token = getBearerToken();

if ($token == USUARIO_KEY) {
	$usuarioController = new UsuarioController();

	if ($rm == 'GET') {
		if (isset($_GET) && count($_GET) > 0) {
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
			$usuarios = $usuarioController->index(['TOP' => 10]);
			if (!$usuarios instanceof ErrorException) {
				sendRes($usuarios);
				exit();
			} else {
				sendRes(null, $usuarios->getMessage(), $_GET);
				exit();
			};
			header("HTTP/1.1 200 OK");
		}
	}

	// Crear un nuevo post
	if ($rm == 'POST') {
		$usuario = $usuarioController->store($_POST);
		sendRes(['ReferenciaID' => $usuario]);
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
