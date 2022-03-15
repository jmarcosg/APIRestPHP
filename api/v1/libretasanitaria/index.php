<?php

use App\Controllers\LibertaSanitariaController;

$libretaController = new LibertaSanitariaController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	$id = $url['id'];
	if (isset($_GET) && $id !== null && $id !== '') {
		$libreta = $libretaController->getSolicitudesWhereId($id);
		if (!$libreta instanceof ErrorException) {
			if ($libreta) {
				sendRes($libreta);
			} else {
				sendRes(null, 'No se encuenta el recurso', ['ReferenciaID' => $id]);
			}
		} else {
			sendRes(null, $libreta->getMessage(), ['ReferenciaID' => $id]);
		};
	} else {
		sendRes(null, 'Error en los parametros', ['ReferenciaID' => $id]);
	}
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
