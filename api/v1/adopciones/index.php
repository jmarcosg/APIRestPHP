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

				echo var_dump($data);

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
			$mensaje = "";
			$cantAnimales = Adop_AnimalesController::index();
			echo "hola index";
			$idCarpeta = count($cantAnimales) + 1;
			echo $cantAnimales;
			// $pathCarpeta = "E:/Dataserver/Replica/projects_files/adopciones/" . $idCarpeta . "/";
			$pathCarpeta = "../../../files/adopciones" . $idCarpeta . "/";

			echo $pathCarpeta;
			$mensaje = "antes de carga de datos";

			$data = [
				'imagen1_path' => $pathCarpeta . "/imagen-grande",
				'imagen2_path' => $pathCarpeta . "/imagen-chica",
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'deshabilitado' => $_POST['deshabilitado'],
				'fecha_ingreso' => $_POST['fecha_ingreso'],
				'fecha_egreso' => $_POST['fecha_egreso'],
				'fecha_modificacion' => $_POST['fecha_modificacion'],
				'fecha_deshabilitado' => $_POST['fecha_deshabilitado']
			];

			$mensaje = "antes de carga de imagenes";
			if (Adop_AnimalesController::store($data)) {
				$animal = Adop_AnimalesController::index($data)[0];
				Adop_AnimalesController::storeImages($_FILES['imagenGrande'], $animal->get([])->value['imagen1_path'], $animal, "imagen1_path");
				Adop_AnimalesController::storeImages($_FILES['imagenGrande'], $animal->get([])->value['imagen2_path'], $animal, "imagen2_path");
				$mensaje = "exito carga";
			} else {
				$mensaje = "error carga";
			}

			echo $mensaje;
			break;

		case '2':
			// Modificar animal
			$idAnimalModificar = $_POST['id'];
			$animal = Adop_AnimalesController::index(['id' => $idAnimalModificar])[0];
			$pathCarpeta = "E:/Dataserver/Replica/projects_files/adopciones/" . $idCarpeta . "/";

			$data = [
				'imagen1_path' => $pathCarpeta . "/imagen-grande",
				'imagen2_path' => $pathCarpeta . "/imagen-chica",
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'fecha_ingreso' => $_POST['fecha_ingreso'],
				'fecha_adopcion' => $_POST['fecha_adopcion'],
				'fecha_modificacion' => $_POST['fecha_modificacion']
			];

			if (Adop_AnimalesController::store($data)) {
				$animal = Adop_AnimalesController::index($data)[0];
				Adop_AnimalesController::storeImages($_FILES['imagenGrande'], $animal->get([])->value['imagen1_path'], $animal, "imagen1_path");
				Adop_AnimalesController::storeImages($_FILES['imagenGrande'], $animal->get([])->value['imagen2_path'], $animal, "imagen2_path");
			}

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
