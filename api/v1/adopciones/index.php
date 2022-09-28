<?php

use App\Controllers\Adopciones\Adop_AnimalController;
use App\Controllers\LicenciaComercial\Lc_SolicitudController;
use App\Controllers\LicenciaComercial\Lc_SolicitudHistorialController;
use App\Controllers\LicenciaComercial\Lc_DocumentoController;
use App\Controllers\LicenciaComercial\Lc_RubroController;
use App\Controllers\Common\TipoDocumentoController;

/* Metodo GET */

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		case '1':
			echo "hola";
			exit;
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	switch ($_POST['action']) {
		case '1':
			echo "hola";
			exit;
	}
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];

	$action = $_PUT['action'];
	unset($_PUT['action']);

	switch ($action) {
		case '1':
			echo "hola";
			exit;
	}


	if (!$lc instanceof ErrorException) {
		$_PUT['id'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $adopciones->getMessage(), ['id' => $id]);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$id = $url['id'];
	$adopciones = $adopSolicitudController->delete($url['id']);
	if (!$adopciones instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $adopciones->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
