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

		case 'get_by_id':
			Lc_SolicitudController::getById();

		case 'get_last_solicitud':
			/* Contribuyente - Ultima solicitud */
			Lc_SolicitudController::getLastSolicitud();

		case 'get_all_rubros':
			/* Listado de Rubros */
			Lc_RubroController::getAllRubros();

		case 'get_all_tipos_documentos':
			/* Listado de Tipos de documentos */
			TipoDocumentoController::getAllTiposDocumentos();

		case 'get_solicitudes_usuario':
			/* Lista todas las solicitudes del usuario */
			Lc_SolicitudController::getSolicitudesUsuario();

			/* ################################### */

		case 'nuevas_verificacion_inicio':
			/* Verificación Inicio Tramite - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_inicio' AND ver_inicio = 0 AND estado NOT LIKE '%rechazado%'");

		case 'aprobadas_verificacion_inicio':
			/* Verificación Inicio Tramite  - Aprobadas */
			Lc_SolicitudController::index("ver_inicio = 1 AND estado NOT LIKE '%rechazado%'");

		case 'rechazadas_verificacion_inicio':
			/* Verificación Inicio Tramite  - Rechazadas */
			Lc_SolicitudController::index("estado = 'inicio_rechazado'");

		case 'retornadas_verificacion_inicio':
			/* Verificación Inicio Tramite  - Retornado */
			Lc_SolicitudController::index("estado LIKE '%retornado_inicio%'");

			/* ################################### */

		case 'nuevas_verificacion_domicilio':
			/* Catastro - Nuevas */
			Lc_SolicitudController::index("estado = 'cat' AND ver_catastro = 0 AND estado NOT LIKE '%rechazado%'");

		case 'aprobadas_verificacion_domicilio':
			/* Catastro - Aprobadas */
			Lc_SolicitudController::index("ver_catastro = 1 AND estado NOT LIKE '%rechazado%'");

		case 'rechazadas_verificacion_domicilio':
			/* Catastro - Rechazadas   */
			Lc_SolicitudController::index("estado = 'cat_rechazado'");

		case 'retornadas_verificacion_domicilio':
			/* Catastro - Retornado   */
			Lc_SolicitudController::index("estado LIKE '%retornado_cat%'");

			/* ################################### */

		case 'nuevas_verificacion_ambiental':
			/* Verificación ambiental - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_amb' AND ver_ambiental = 0 AND estado NOT LIKE '%rechazado%'");

		case 'aprobadas_verificacion_ambiental':
			/* Verificación ambiental - Aprobadas */
			Lc_SolicitudController::index("ver_ambiental = 1 AND estado NOT LIKE '%rechazado%'");

		case 'rechazadas_verificacion_ambiental':
			/* Verificación ambiental - Rechazadas */
			Lc_SolicitudController::index("estado = 'ambiental_rechazado'");

			/* ################################### */

		case 'nuevas_verificacion_rubros':
			/* Verificador Rubros - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_rubros' AND ver_rubros = 0 AND estado NOT LIKE '%rechazado%'");

		case 'aprobadas_verificacion_rubros':
			/* Verificador Rubros - Aprobadas */
			Lc_SolicitudController::index("ver_rubros = '1' AND estado NOT LIKE '%rechazado%'");

		case 'rechazadas_verificacion_rubros':
			/* Verificador Rubros - Rechazadas  */
			Lc_SolicitudController::index("estado = 'rubros_rechazado'");

			/* ################################### */

		case 'nuevas_verificacion_documentos':
			/* Verificación documentos - Nuevas */
			Lc_SolicitudController::index("estado = 'ver_doc' AND ver_documentos = 0 AND estado NOT LIKE '%rechazado%'");

		case 'aprobadas_verificacion_documentos':
			/* Verificación documentos - Aprobadas */
			Lc_SolicitudController::index("ver_documentos = 1 AND estado NOT LIKE '%rechazado%'");

		case 'rechazadas_verificacion_documentos':
			/* Verificación documentos - Rechazadas */
			Lc_SolicitudController::index("estado = 'doc_rechazado'");

		case 'retornadas_verificacion_documentos':
			/* Verificación documentos - Retornado */
			Lc_SolicitudController::index("estado LIKE '%retornado_documentos%'");

			/* ################################### */

		case 'admin_todos':
			/* Administrador - Todos */
			Lc_SolicitudController::index("1 = 1");

		case 'auditoria_todos':
			/* Auditoria - Todos */
			Lc_SolicitudController::index("estado = 'finalizado'");

		case 'get_sol_pdf':
			Lc_SolicitudController::getSolicitudPdf($_GET['id']);

		case 'get_historial_sol':
			/* Administrador */
			Lc_SolicitudHistorialController::getHistorialBySol($_GET['id']);

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	switch ($action) {
		case 'store_solicitud':
			Lc_SolicitudController::store();
		case 'update_documentacion':
			Lc_DocumentoController::updateDocumentacion();
		case 'set_view_historial':
			Lc_SolicitudHistorialController::setViewHistorial($_POST['id']);
		case 'update_notas':
			Lc_DocumentoController::updateNotas();

		case 'datos_personales':
			/* Datos personales */
			Lc_SolicitudController::datosPersonales();

		case 'actividad':
			/* Nomenclatura y rubros */
			Lc_SolicitudController::actividad();

		case 'init_veri_update':
			/* Verificacion inicial - Evaluacion */
			Lc_SolicitudController::initVeriUpdate();
			break;

		case 'documentacion':
			/* Documentacion */
			Lc_SolicitudController::documentacion();

		case 'rubros_veri_update':
			/* Verificacion de rubros - Aprobacion */
			Lc_SolicitudController::rubrosVeriUpdate();

		case 'catastro_veri_update':
			/* Catastro - Aprobacion */
			Lc_SolicitudController::catastroVeriUpdate();

		case 'ambiental_veri_update':
			/* Catastro - Verificacion ambiental */
			Lc_SolicitudController::ambientalVeriUpdate();

		case 'documentos_veri_update':
			/* Catastro - Verificacion ambiental */
			Lc_SolicitudController::documentosVeriUpdate();

		case 'eval_documento':
			/* GeneralDocumentos - Evauluacion de documento */
			Lc_SolicitudController::evalDocumento();
			break;

		case 'set_expediente':
			/* Auditoria - Set Expediente */
			Lc_SolicitudController::setExpediente();

		case 'set_licencia':
			/* Auditoria - Set Expediente */
			Lc_SolicitudController::setLicenciaComercial();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_POST);
	}
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
