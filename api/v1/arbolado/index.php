<?php

use App\Controllers\Arbolado\Arb_EvaluacionController;
use App\Controllers\Arbolado\Arb_InspectorController;
use App\Controllers\Arbolado\Arb_SolicitudController;
use App\Controllers\Arbolado\Arb_PodadorController;

/* Metodo GET */

if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0 && isset($_GET['action'])) {
		$action = $_GET['action'];
		unset($_GET['action']);

		switch ($action) {
			case '0':
				/* Obtenemos todas las solicitudes de poda */
				Arb_SolicitudController::index();
				break;

			case '1':
				/* Obtenemos todas las solicitudes de poda nuevas */
				Arb_SolicitudController::index("estado = 'nuevo'");
				break;

			case '2':
				/* Obtenemos todas las solicitudes de poda aprobadas */
				Arb_SolicitudController::index("estado = 'aprobado'");
				break;

			case '3':
				/* Obtenemos todas las solicitudes de poda rechazadas */
				Arb_SolicitudController::index("estado = 'rechazado'");
				break;

			case '4':
				/* Obtenemos todas las solicitudes de poda del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("id_usuario = $id_usuario");
				break;

			case '5':
				/* Obtenemos todas las solicitudes de poda nuevas  del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("id_usuario = $id_usuario AND estado = 'nuevo'");
				break;

			case '6':
				/* Obtenemos todas las solicitudes de poda aprobadas  del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("id_usuario = $id_usuario AND estado = 'aprobado'");
				break;

			case '7':
				/* Obtenemos todas las solicitudes de poda rechazadas  del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("estado = 'rechazado'");
				break;

			case '8':
				/* Solicitud de poda por ID */
				Arb_SolicitudController::getById($_GET['id']);
				break;

			case '9':
				/* Solitides de podador - Todas */
				Arb_PodadorController::index();
				break;

			case '10':
				/* Solitides de podador - Nuevas */
				Arb_PodadorController::index("estado = 'nuevo'");
				break;

			case '11':
				/* Solitides de podador - Aprobadas */
				Arb_PodadorController::index("estado = 'aprobado'");
				break;

			case '12':
				/* Solitides de podador - Rechazadas */
				Arb_PodadorController::index("estado = 'rechazado'");
				break;

			case '13':
				/* Solitides de podador - Deshabilitados */
				Arb_PodadorController::getDeshabilitados();
				break;

			case '14':
				/* Solicitud de podador por ID */
				Arb_PodadorController::getById($_GET['id']);
				break;

			case '15':
				/* Solicitud de poda por ID */
				Arb_EvaluacionController::getEvaluacionMsg();
				break;

			case '16':
				/* Solicitud de poda por ID */
				Arb_EvaluacionController::getPresetEvaluacion();
				break;

			case '17':
				/* Detalle de la solicitud */
				Arb_PodadorController::getEstadoSolicitudDetalle();

			case '18':
				/* Detalle de la solicitud*/
				Arb_EvaluacionController::index($_GET, ['order' => ' ORDER BY id DESC ']);

			case '19':
				/* Detalle de la solicitud*/
				Arb_EvaluacionController::index(['id_podador' => null]);

			case '20':
				/* Listado de inspectores */
				Arb_InspectorController::index();

			default:
				$error = new ErrorException('El action no es valido');
				sendRes(null, $error->getMessage(), $_GET);
				exit;
		}
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	switch ($action) {
		case '0':
			/* Guardamos una solicitud de poda */
			Arb_SolicitudController::store($_POST);
			break;

		case '1':
			/* Guardamos una solicitud de podador */
			Arb_PodadorController::store();
			break;

		case '2':
			/* Guardamos una evaluacion */
			Arb_EvaluacionController::saveEvaluacion();
			break;

		case '3':
			/* Guardamos un inspector */
			Arb_InspectorController::store();
			break;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$action = $_PUT['action'];
	unset($_PUT['action']);

	switch ($action) {
		case '0':
			Arb_SolicitudController::update($_PUT, $url['id']);
			break;

		case '1':
			Arb_PodadorController::update($_PUT, $url['id']);
			break;
	}
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	switch ($_GET['action']) {
		case '0':
			Arb_SolicitudController::delete($url['id']);
			break;
		case '3':
			Arb_InspectorController::delete($url['id']);
			break;

		default:
			# code...
			break;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
