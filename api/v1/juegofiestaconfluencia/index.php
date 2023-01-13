<?php

use App\Controllers\JuegoFiestaConfluencia\MEMCONF_UsuarioController;
use App\Controllers\JuegoFiestaConfluencia\MEMCONF_PartidaController;
use App\Controllers\JuegoFiestaConfluencia\MEMCONF_ConfiguracionController;

$dotenv = \Dotenv\Dotenv::createImmutable('./juegofiestaconfluencia/');
$dotenv->load();

include './juegofiestaconfluencia/config.php';

/**
 * *Metodo GET
 */

if ($url['method'] == "GET") {
	if (isset($_GET['action'])) {

		switch ($_GET['action']) {
			case 't':
				echo "hola get";
				exit;

			default:
				$error = new ErrorException('El action no es valido');
				sendRes(null, $error->getMessage(), $_GET);
				exit;
		}
	} else {
		$data = [
			'error' => "Imposible conseguir esos datos"
		];
	}

	if (!$data instanceof ErrorException) {
		sendRes($data);
	} else {
		sendRes(null, "No se encuentra el registro buscado");
	}
	exit;
}

/**
 * *Metodo POST
 */

if ($url['method'] == "POST") {
	switch ($_POST['action']) {
		case 't':
			echo "test post";
			exit;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
