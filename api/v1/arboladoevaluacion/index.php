<?php

use App\Controllers\Arbolado\Arb_EvaluacionController;
use App\Controllers\Arbolado\Arb_PodadorController;

$arbEvaluacionController = new Arb_EvaluacionController();
$arbPodadorController = new Arb_PodadorController();

/* Metodo GET */
if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0 && isset($_GET['action'])) {
		$action = $_GET['action'];
		unset($_GET['action']);

		switch ($action) {
			case '0':
				/* Obtenemos todas las solicitudes, o funcion del estado */
				$_GET['TOP'] = 1000;
				$evaluacion = $arbEvaluacionController->index($_GET, ['order' => ' ORDER BY id DESC ']);
				break;

			case '1':
				/* Obtenemos una solicitud puntual */
				$evaluacion = $arbEvaluacionController->index(['id_podador' => '-1']);
				break;

			case '2':
				/* Obtenemos una solicitud puntual */
				$params = ['id_wappersonas' => $_GET['id_wappersonas'], 'TOP' => 1, 'id_podador' => "-1"];
				$op = ['order' => ' ORDER BY id DESC '];
				$evaluacion = $arbEvaluacionController->index($params, $op);

				if (count($evaluacion) > 0) {
					$evaluacion = $evaluacion[0];
				} else {
					$evaluacion = ['msg' => 'No presenta evaluación'];
				}

				break;

			case '3':
				/* Obtenemos una solicitud puntual */
				$params = ['id_wappersonas' => $_GET['id_wappersonas'], 'TOP' => 1, 'id_podador' => "-1"];
				$op = ['order' => ' ORDER BY id DESC '];
				$evaluacion = $arbEvaluacionController->index($params, $op);

				if (count($evaluacion) > 0) {
					$evaluacion = ['msg' => 'Ya presenta una evaluación cargada'];
				} else {
					$evaluacion = ['msg' => null];
				}

				break;

			default:
				$arbolado = new ErrorException('El action no es valido');
				break;
		}


		if (!$evaluacion instanceof ErrorException) {
			if ($evaluacion !== false) {
				sendRes($evaluacion);
			} else {
				sendRes(null, 'No se encontro la evaluación', $_GET);
			}
		} else {
			sendRes(null, $arbolado->getMessage(), $_GET);
		};
	} else {
	}
	eClean();
}

/* Metodo POST */
if ($url['method'] == 'POST') {

	/* Configuracion de los parametros */
	$params = ['id_wappersonas' => $_POST['id_wappersonas'], 'TOP' => 1, 'id_podador' => "-1"];
	$op = ['order' => ' ORDER BY id DESC '];

	/* buscamos la evaluacion */
	$evaluacion = $arbEvaluacionController->index($params, $op);

	/* Si no encontramos una evaluacion, lo guardamos */
	if (!count($evaluacion) > 0) {
		$id = $arbEvaluacionController->store($_POST);
		if (!$id instanceof ErrorException) {
			sendRes(['id' => $id]);
		} else {
			sendRes(null, $id->getMessage(), $_GET);
		}
		exit;
	}

	/* Buscamos la solicitu del podador */
	$solicitudPodador = $arbPodadorController->index($params, $op);

	/* Si existe una solicitud, analizamos situacion */
	if (count($solicitudPodador) > 0) {
		$solicitudPodador = $solicitudPodador[0];
		$fechaVenc = $solicitudPodador['fecha_vencimiento'];

		/* Si no esta vigente, guardamos la evaluacion */
		if (!esVigente($fechaVenc)) {
			$id = $arbEvaluacionController->store($_POST);
			if (!$id instanceof ErrorException) {
				sendRes(['id' => $id]);
			} else {
				sendRes(null, $id->getMessage(), $_GET);
			}
			exit;
		} else {
			$response = [
				'msg' =>  'Presenta una solicitud vigente',
				'solicitudPodador' => $solicitudPodador
			];
			sendRes($response);
			exit;
		}
	} else {
		$id = $arbEvaluacionController->store($_POST);
		if (!$id instanceof ErrorException) {
			sendRes(['id' => $id]);
		} else {
			sendRes(null, $id->getMessage(), $_GET);
		}
		exit;
	}
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];
	$arbolado = $arbPodadorController->update($_PUT, $id);

	if (!$arbolado instanceof ErrorException) {
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
	$arbolado = $arbPodadorController->delete($url['id']);
	if (!$arbolado instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
