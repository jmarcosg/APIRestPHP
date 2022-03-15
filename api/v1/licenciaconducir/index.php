<?php

use App\Controllers\LicenciaConducirController;

$licenciaConducirController = new LicenciaConducirController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	$id = $url['id'];
	if (isset($_GET) && $id !== null && $id !== '') {
		$licencia = $licenciaConducirController->getByReferenciaId($id);
		if (!$licencia instanceof ErrorException) {
			if ($licencia) {
				sendRes($licencia);
			} else {
				sendRes(null, 'No se encuenta el recurso', ['Documento' => $id]);
			}
		} else {
			sendRes(null, $licencia->getMessage(), ['Documento' => $id]);
		};
	} else {
		sendRes(null, 'Error en los parametros', ['Documento' => $id]);
	}
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
