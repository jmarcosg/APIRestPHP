<?php

use App\Controllers\RenaperController;

$renaperController = new RenaperController();

/* Metodo GET */
if ($token == RENAPER_KEY && $url['method'] == 'GET') {
	if (isset($_GET) && isset($_GET['sexo']) && isset($_GET['dni'])) {
		$renaper = $renaperController->getData($_GET['sexo'], $_GET['dni']);
		if (!$renaper instanceof ErrorException) {
			sendRes($renaper);
		} else {
			sendRes(null, $renaper->getMessage(), $_GET);
		};
	} else {
		sendRes(null, 'Error en los parametros', $_GET);
	}
	exit();
}

if ($token != RENAPER_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 200 Bad Request");
}
exit();
