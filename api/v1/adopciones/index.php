<?php

use App\Controllers\Adopciones\Adop_AnimalesController;
use App\Controllers\Adopciones\Adop_VecinosController;
use App\Controllers\Adopciones\Adop_AdopcionesController;

/* Metodo GET */

if ($url['method'] == "GET") {
	if (isset($_GET['action'])) {

		switch ($_GET['action']) {
			case '1':
				// Listado de animales
				$animalesController = new Adop_AnimalesController();
				$data = $animalesController->index();

				if (count($data) == 0) {
					$data = [
						'animal' => null,
						'error' => "Animal no encontrado"
					];
				} else {
					$data['error'] = null;
				}
				break;
			case '2':
				// Listado de vecinos
				$data = Adop_VecinosController::index();

				if (count($data) == 0) {
					$data = [
						'vecino' => null,
						'error' => "Vecino no encontrado"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case '3':
				// Listado de adopciones
				$data = Adop_AdopcionesController::index();

				if (count($data) == 0) {
					$data = [
						'adopcion' => null,
						'error' => "Adopcion no encontrada"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 't':
				echo "hola get";
				exit;

			default:
				$error = new ErrorException('El action no es valido');
				sendRes(null, $error->getMessage(), $_GET);
				exit;
		}
	} else {
		$data = [
			'adopcion' => null,
			'error' => "Adopcion no encontrada"
		];
	}

	echo json_encode($data);
}

/* Metodo POST */

if ($url['method'] == "POST") {
	switch ($_POST['action']) {
		case '1':
			// Cargar animal
			$animales = Adop_AnimalesController::index();

			$data = [
				'imagen1_path' => $_FILES['imagen1']['name'],
				'imagen2_path' => $_FILES['imagen2']['name'],
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['edad'],
				'raza' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'deshabilitado' => $_POST['deshabilitado'],
				'fecha_ingreso' => $_POST['fecha_ingreso'],
				'fecha_modificacion' => $_POST['fecha_modificacion'],
				'fecha_deshabilitado' => $_POST['fecha_deshabilitado']
			];

			$id = Adop_AnimalesController::store($data);

			// print_r($id);
			// die();

			if (!$id instanceof ErrorException) {
				$imagen1Cargada = Adop_AnimalesController::storeImage($_FILES['imagen1'], $id, "imagen1_path");
				//! No esta renombrando ni ¿reconociendo? la segunda imagen a subir
				$imagen2Cargada = Adop_AnimalesController::storeImage($_FILES['imagen2'], $id, "imagen2_path");

				if ($imagen1Cargada && $imagen2Cargada) {
					$mensaje = "exito carga y guardado de imagenes";
				} else {
					$mensaje = "error en carga y guardado de imagenes";
				}
			} else {
				$mensaje = $id->getMessage();
				// $mensaje = "prueba error";
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			break;

		case '2':
			// Modificar animal
			$idAnimalModificar = $_POST['id'];
			$animal = Adop_AnimalesController::index(['id' => $idAnimalModificar])[0];

			$data = [
				'imagen1_path' => $_POST['imagen1_path'],
				'imagen2_path' => $_POST['imagen2_path'],
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['edad'],
				'raza' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'deshabilitado' => $_POST['deshabilitado'],
				'fecha_ingreso' => $_POST['fecha_ingreso'],
				'fecha_modificacion' => $_POST['fecha_modificacion'],
				'fecha_deshabilitado' => $_POST['fecha_deshabilitado']
			];


			$id = Adop_AnimalesController::store($data);

			if (!$id instanceof ErrorException) {
				$animal = Adop_AnimalesController::index($data);
				Adop_AnimalesController::storeImage($_FILES['imagen1_path'], $id, $animal, "imagen1_path");
				Adop_AnimalesController::storeImage($_FILES['imagen2_path'], $id, $animal, "imagen2_path");
				$mensaje = "exito carga";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			break;

		case '3':
			echo "hola delete";
			break;

		case 't':
			echo "test post";
			exit;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

/* Metodo DELETE */

if ($url['method'] == 'DELETE') {
	$id = $url['id'];
	$animal = $arbSolicitudController->delete($url['id']);
	if (!$arbolado instanceof ErrorException) {
		sendRes(['ReferenciaID' => $id]);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

header("HTTP/1.1 200 Bad Request");

eClean();
