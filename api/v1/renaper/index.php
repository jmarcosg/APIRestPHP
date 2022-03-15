<?php

use App\Controllers\RenaperController;

$renaperController = new RenaperController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && isset($_GET['sexo']) && isset($_GET['dni'])) {

		if (!isset($_GET['type'])) $_GET['type'] = '';
		switch ($_GET['type']) {
			case 'imagen';
				$renaper = $renaperController->getImage($_GET['sexo'], $_GET['dni']);
				break;
			case 'person';
				$renaper = $renaperController->getPersonData($_GET['sexo'], $_GET['dni']);
				break;
			default:
				$renaper = $renaperController->getData($_GET['sexo'], $_GET['dni']);
				break;
		}

		if (!$renaper instanceof ErrorException) {
			sendRes($renaper);
		} else {
			sendRes(null, $renaper->getMessage(), $_GET);
		};
	} else {
		sendRes(null, 'Error en los parametros', $_GET);
	}
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
