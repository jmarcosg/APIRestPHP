<?php

use App\Controllers\TributoController;

$tributoController = new TributoController();

if ($url['method'] == 'POST') {
	if ($_POST['type'] == 'saveStats') {
		$tributoStats = $tributoController->save($_POST);
	}

	if ($_POST['type'] == 'sendEmailMensual') {
		$tributoStats = $tributoController->sendEmailMensual($_POST);
	}

	if ($_POST['type'] == 'sendEmailSemestral') {
		$tributoStats = $tributoController->sendEmailSemestral($_POST);
	}
	header("HTTP/1.1 200 OK");
	exit();
}

header("HTTP/1.1 200 Bad Request");

eClean();
