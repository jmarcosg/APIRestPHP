<?php

use App\Controllers\LicenciaComercial\Lc_SolicitudController;
use App\Controllers\LicenciaComercial\Lc_SolicitudHistorialController;
use App\Controllers\LicenciaComercial\Lc_DocumentoController;
use App\Controllers\LicenciaComercial\Lc_RubroController;
use App\Controllers\Common\TipoDocumentoController;

$dotenv = \Dotenv\Dotenv::createImmutable('./licenciacomercial/');
$dotenv->load();

include './licenciacomercial/config.php';

/* Metodo GET */
if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		case '1':
			Lc_SolicitudController::getById();

		case '2':
			/* Contribuyente - Ultima solicitud */
			Lc_SolicitudController::getLastSolicitud();

		case '3':
			/* Listado de Rubros */
			Lc_RubroController::getAllRubros();

		case '4':
			/* Listado de Tipos de documentos */
			TipoDocumentoController::getAllTiposDocumentos();

		case '5':
			/* Lista todas las solicitudes del usuario */
			Lc_SolicitudController::getSolicitudesUsuario();

			/* ################################### */

		case '10':
			/* Verificación Inicio Tramite - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_inicio' AND ver_inicio = 0 AND estado NOT LIKE '%rechazado%'");

		case '11':
			/* Verificación Inicio Tramite  - Aprobadas */
			Lc_SolicitudController::index("ver_inicio = 1 AND estado NOT LIKE '%rechazado%'");

		case '12':
			/* Verificación Inicio Tramite  - Rechazadas */
			Lc_SolicitudController::index("estado = 'inicio_rechazado'");

		case '13':
			/* Verificación Inicio Tramite  - Retornado */
			Lc_SolicitudController::index("estado LIKE '%retornado_inicio%'");

			/* ################################### */

		case '20':
			/* Catastro - Nuevas */
			Lc_SolicitudController::index("estado = 'cat' AND ver_catastro = 0 AND estado NOT LIKE '%rechazado%'");

		case '21':
			/* Catastro - Aprobadas */
			Lc_SolicitudController::index("ver_catastro = 1 AND estado NOT LIKE '%rechazado%'");

		case '22':
			/* Catastro - Rechazadas   */
			Lc_SolicitudController::index("estado = 'cat_rechazado'");

		case '23':
			/* Catastro - Retornado   */
			Lc_SolicitudController::index("estado LIKE '%retornado_cat%'");

			/* ################################### */

		case '30':
			/* Verificación ambiental - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_amb' AND ver_ambiental = 0 AND estado NOT LIKE '%rechazado%'");

		case '31':
			/* Verificación ambiental - Aprobadas */
			Lc_SolicitudController::index("ver_ambiental = 1 AND estado NOT LIKE '%rechazado%'");

		case '32':
			/* Verificación ambiental - Rechazadas */
			Lc_SolicitudController::index("estado = 'ambiental_rechazado'");

			/* ################################### */

		case '40':
			/* Verificador Rubros - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_rubros' AND ver_rubros = 0 AND estado NOT LIKE '%rechazado%'");

		case '41':
			/* Verificador Rubros - Aprobadas */
			Lc_SolicitudController::index("ver_rubros = '1' AND estado NOT LIKE '%rechazado%'");

		case '42':
			/* Verificador Rubros - Rechazadas  */
			Lc_SolicitudController::index("estado = 'rubros_rechazado'");

			/* ################################### */

		case '50':
			/* Verificación documentos - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_doc' AND ver_documentos = 0 AND estado NOT LIKE '%rechazado%'");

		case '51':
			/* Verificación documentos - Aprobadas */
			Lc_SolicitudController::index("ver_documentos = 1 AND estado NOT LIKE '%rechazado%'");

		case '52':
			/* Verificación documentos - Rechazadas */
			Lc_SolicitudController::index("estado = 'doc_rechazado'");

		case '53':
			/* Verificación documentos - Retornado */
			Lc_SolicitudController::index("estado LIKE '%retornado_documentos%'");

			/* ################################### */

		case '70':
			/* Administrador - Todos */
			Lc_SolicitudController::index("1 = 1");

		case '80':
			/* Auditoria - Todos */
			Lc_SolicitudController::index("estado = 'finalizado'");

		case '81':
			Lc_SolicitudController::getSolicitudPdf($_GET['id']);

		case '100':
			/* Administrador */
			Lc_SolicitudHistorialController::getHistorialBySol($_GET['id']);

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	switch ($_POST['action']) {
		case '1':
			Lc_SolicitudController::store();
		case '3':
			Lc_DocumentoController::updateDocumentacion();
		case '4':
			Lc_SolicitudHistorialController::setViewHistorial($_POST['id']);
		case '5':
			Lc_DocumentoController::updateNotas();
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
			/* Datos personales */
			Lc_SolicitudController::datosPersonales($_PUT, $id);
			break;

		case '2':
			/* Nomenclatura y rubros */
			Lc_SolicitudController::actividad($_PUT, $id);
			break;

		case '3':
			/* Documentacion */
			Lc_SolicitudController::documentacion($_PUT, $id);
			break;

		case '6':
			/* Verificacion de rubros - Aprobacion */
			Lc_SolicitudController::rubrosVeriUpdate($_PUT, $id);
			break;

		case '7':
			/* Catastro - Aprobacion */
			Lc_SolicitudController::catastroVeriUpdate($_PUT, $id);
			break;

		case '8':
			/* Catastro - Verificacion ambiental */
			Lc_SolicitudController::ambientalVeriUpdate($_PUT, $id);
			break;

		case '9':
			/* Catastro - Verificacion ambiental */
			Lc_SolicitudController::documentosVeriUpdate($_PUT, $id);
			break;

		case '10':
			/* GeneralDocumentos - Evauluacion de documento */
			Lc_SolicitudController::evalDocumento($_PUT, $id);
			break;

		case '11':
			/* GeneralDocumentos - Evauluacion de documento */
			Lc_SolicitudController::initVeriUpdate($_PUT, $id);
			break;

		case '13':
			/* Auditoria - Set Expediente */
			Lc_SolicitudController::setExpediente($_PUT, $id);
			break;

		case '14':
			/* Auditoria - Set Expediente */
			Lc_SolicitudController::setLicenciaComercial($_PUT, $id);
			break;

		default:
			# code...
			break;
	}


	if (!$lc instanceof ErrorException) {
		$_PUT['id'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $arbolado->getMessage(), ['id' => $id]);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	$id = $url['id'];
	$arbolado = $arbSolicitudController->delete($url['id']);
	if (!$arbolado instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
