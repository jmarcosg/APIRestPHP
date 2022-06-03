<?php

use App\Controllers\RenaperController;

$renaperController = new RenaperController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && isset($_GET['sexo']) && isset($_GET['dni'])) {

		if (!isset($_GET['type'])) $_GET['type'] = '';

		if (isset($_GET['tramite'])) {
			switch ($_GET['type']) {
				case 'imagen';
					$renaper = $renaperController->getImageTramite($_GET['sexo'], $_GET['dni'], $_GET['tramite']);
					break;
				case 'person';
					$renaper = $renaperController->getPersonDataTramite($_GET['sexo'], $_GET['dni'], $_GET['tramite']);
					break;
				default:
					$renaper = $renaperController->getDataTramite($_GET['sexo'], $_GET['dni'], $_GET['tramite']);
					break;
			}
		} else {
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

$tokenSeg = 'f10e2821bbbea527ea02200352313bc059445190';
if ($url['method'] == 'POST' && $_POST['token'] == $tokenSeg) {
	$token = $renaperController->getTokenRenaper();

	if (!$token instanceof ErrorException) {
		sendRes($token);
	} else {
		sendRes(null, $token->getMessage(), $_POST);
	};

	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
