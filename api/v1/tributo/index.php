<?php

use App\Controllers\TributoController;

$tributoController = new TributoController();

if ($url['method'] == 'POST') {
	if ($_POST['type'] == 'saveStats') {
		$tributoStats = $tributoController->save($_POST);
	}

	if ($_POST['type'] == 'sendEmailMensual') {
		$emailMensual = $tributoController->sendEmailMensual($_POST);
		sendRes($emailMensual);
	}

	if ($_POST['type'] == 'sendEmailSemestral') {
		$emailSemestral = $tributoController->sendEmailSemestral($_POST);
		sendRes($emailSemestral);
	}
	header("HTTP/1.1 200 OK");
	exit();
}

header("HTTP/1.1 200 Bad Request");

eClean();
