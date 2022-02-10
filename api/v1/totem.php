<?php

include '../../app/config/global.php';

if ($rm == 'GET') {
	if (isset($_GET['id'])) {
		echo 'asdas';
		exit();
	} else {
		echo getBearerToken();
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
//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");


