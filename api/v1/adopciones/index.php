<?php

use App\Controllers\Adopciones\Adop_AnimalesController;
use App\Controllers\Adopciones\Adop_VecinosController;
use App\Controllers\Adopciones\Adop_AdopcionesController;

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

if ($url['method'] == "POST") {
	switch ($_POST['action']) {
		case '1':
			// Cargar animal
			$cantAnimales = Adop_AnimalesController::index();
			$idCarpeta = count($cantAnimales) + 1;

			$data = [
				'imagen1_path' => "/imagen-grande",
				'imagen2_path' => "/imagen-chica",
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
				// print_r($animal);
				// die();
				Adop_AnimalesController::storeImages($_FILES['imagen1_path'], $id, $animal, "imagen1_path");
				Adop_AnimalesController::storeImages($_FILES['imagen2_path'], $id, $animal, "imagen2_path");
				$mensaje = "exito carga";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			break;

		case '2':
			// Modificar animal
			$idAnimalModificar = $_POST['id'];
			$animal = Adop_AnimalesController::index(['id' => $idAnimalModificar])[0];

			$data = [
				'imagen1_path' => "/imagen-grande",
				'imagen2_path' => "/imagen-chica",
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
				// print_r($animal);
				// die();
				Adop_AnimalesController::storeImages($_FILES['imagen1_path'], $id, $animal, "imagen1_path");
				Adop_AnimalesController::storeImages($_FILES['imagen2_path'], $id, $animal, "imagen2_path");
				$mensaje = "exito carga";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			break;

		case 't':
			echo "hola post";
			exit;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
