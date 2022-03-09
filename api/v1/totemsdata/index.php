<?php

use App\Controllers\TotemsDataController;

$totemsDataController = new TotemsDataController();

/* Metodo GET */
if ($token == TOTEMSDATA_KEY && $url['method'] == 'GET') {
	if (count($_GET) > 0 && isset($_GET['group']) && isset($_GET['totem'])) {

		$totem = $_GET['totem'];

		if (isset($_GET['year']) || isset($_GET['month']) || isset($_GET['day']) || isset($_GET['hour'])) {
			if ($_GET['group'] == 'month')
				$totemsData = $totemsDataController->groupByMonth($_GET['year'], $totem);

			if ($_GET['group'] == 'day')
				$totemsData = $totemsDataController->groupByDay($_GET['year'], $_GET['month'], $totem);

			/* if ($_GET['group'] == 'hour') $totemsData = $totemsDataController->groupByMonth($year); */
		} else {
			sendRes(null, 'Error en los parametros', $_GET);
			exit();
		}


		if (!$totemsData instanceof ErrorException) {
			if ($totemsData) {
				sendRes($totemsData);
			} else {
				sendRes(null, 'No se encuenta el recurso', $_GET);
			}
		} else {
			sendRes(null, $totemsData->getMessage(), $_GET);
		};
	} else {
		sendRes(null, 'Error en los parametros', $_GET);
	}
	exit();
}

if ($token != ACARREO_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 200 Bad Request");
}
exit();
