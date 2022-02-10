<?php

include '../../app/config/global.php';
$token = getBearerToken();

if ($token == TOTEM_KEY) {
	if ($rm == 'GET') {
		if (isset($_GET['id'])) {
			echo 'asdas';
			exit();
		} else {
			/* echo getBearerToken(); */
			exit();
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
	sendRes(['error' => 'Problema']);
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
