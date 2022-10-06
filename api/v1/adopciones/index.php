<?php

use App\Controllers\Adopciones\Adop_AnimalesController;
use App\Controllers\Adopciones\Adop_VecinosController;
use App\Controllers\Adopciones\Adop_AdopcionesController;

/* Metodo GET */

if ($url['method'] == "GET") {
	if (isset($_GET['action'])) {

		switch ($_GET['action']) {
			case 'an1':
				// Listado de animales
				$animalesController = new Adop_AnimalesController();
				$data = $animalesController->index();

				if (count($data) == 0) {
					$data = [
						'animal' => null,
						'error' => "No hay registros de animales"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 'an2':
				// Obtener animal por id
				$animalesController = new Adop_AnimalesController();
				$data = Adop_AnimalesController::index(['id' => $_GET['id']]);

				if (count($data) == 0) {
					$data = [
						'animal' => null,
						'error' => "Animal no encontrado"
					];
				} else {
					$data = $data[0];
					$data['error'] = null;
				}
				break;

			case 'v1':
				// Listado de vecinos
				$data = Adop_VecinosController::index();

				if (count($data) == 0) {
					$data = [
						'vecino' => null,
						'error' => "No hay registros de vecinos"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 'v2':
				// Obtener vecino por id
				$vecinosController = new Adop_VecinosController();
				$data = Adop_VecinosController::index(['id' => $_GET['id']]);

				if (count($data) == 0) {
					$data = [
						'vecino' => null,
						'error' => "Vecino no encontrado"
					];
				} else {
					$data = $data[0];
					$data['error'] = null;
				}
				break;

			case 'ad1':
				// Listado de adopciones
				$data = Adop_AdopcionesController::index();

				if (count($data) == 0) {
					$data = [
						'adopcion' => null,
						'error' => "No hay registros de adopciones"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 'ad2':
				// Obtener adopcion por id
				$adopcionesController = new Adop_AdopcionesController();
				$data = Adop_AdopcionesController::index(['id' => $_GET['id']]);

				if (count($data) == 0) {
					$data = [
						'adopcion' => null,
						'error' => "Adopcion no encontrada"
					];
				} else {
					$data = $data[0];
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
		case 'an1':
			// Cargar animal
			$data = [
				'imagen1_path' => "a",
				'imagen2_path' => "a",
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['edad'],
				'raza' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'deshabilitado' => $_POST['deshabilitado'],
				'fecha_ingreso' => date('Y-m-d H:i:s'),
				'fecha_modificacion' => null,
				'fecha_deshabilitado' => null
			];

			$id = Adop_AnimalesController::store($data);

			if (!$id instanceof ErrorException) {
				$imagenCargada = Adop_AnimalesController::storeImage($_FILES['imagen1'], $id, "imagen1_path");
				$imagen1 = $imagenCargada;
				if ($imagenCargada) {
					$imagenCargada = Adop_AnimalesController::storeImage($_FILES['imagen2'], $id, "imagen2_path");
					$imagen2 = $imagenCargada;

					if ($imagenCargada) {
					}
				}
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			break;


		case 'an2':
			// Modificar animal
			$idAnimalModificar = $_POST['id'];
			$animal = Adop_AnimalesController::index(['id' => $idAnimalModificar])[0];
			$fechaIngreso = date($animal['fecha_ingreso']);
			$fechaModificado = date('Y-m-d H:i:s');

			$data = [
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['edad'],
				'raza' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'deshabilitado' => $_POST['deshabilitado'],
				'fecha_ingreso' => $fechaIngreso,
				'fecha_modificacion' => $fechaModificado,
				'fecha_deshabilitado' => null
			];

			$id = Adop_AnimalesController::update($data, $idAnimalModificar);

			if (!$id instanceof ErrorException) {
				$imagenCargada = Adop_AnimalesController::storeImage($_FILES['imagen1'], $idAnimalModificar, "imagen1_path");
				$imagen1 = $imagenCargada;
				if ($imagenCargada) {
					$imagenCargada = Adop_AnimalesController::storeImage($_FILES['imagen2'], $idAnimalModificar, "imagen2_path");
					$imagen2 = $imagenCargada;

					if ($imagenCargada) {
					}
				}
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			break;

		case 'an3':
			// Eliminar animal
			$idAnimalEliminar = $_POST['id'];

			$animalesController = new Adop_AnimalesController();
			$animal = $animalesController->delete($idAnimalEliminar);

			if (!$animal instanceof ErrorException) {
				$mensaje = "Animal eliminado correctamente";
			} else {
				sendRes(null, $animal->getMessage(), null);
			};

			echo $mensaje;
			break;

		case 'v1':
			// Cargar vecino
			$vecinos = Adop_VecinosController::index();

			// Verifico que el vecino a cargar no exista en la bd
			$vecinoDistinto = Adop_VecinosController::index(['dni' => $_POST['dni']]);

			if (count($vecinoDistinto) == 0) {
				$data = [
					'nombre' => deutf8ize($_POST['nombre']),
					'dni' => $_POST['dni'],
					'email' => $_POST['email'],
					'email_alternativo' => $_POST['email_alternativo'],
					'telefono' => $_POST['telefono'],
					'telefono_alternativo' => $_POST['telefono_alternativo'],
					'ciudad' => deutf8ize($_POST['ciudad']),
					'domicilio' => deutf8ize($_POST['domicilio'])
				];

				$id = Adop_VecinosController::store($data);

				if (!$id instanceof ErrorException) {
					$mensaje = "exito carga vecino";
				} else {
					$mensaje = $id->getMessage();
					// $mensaje = "prueba error";
					logFileEE('prueba', $id, null, null);
				}
			} else {
				$mensaje = "vecino ya cargado";
			}

			echo $mensaje;
			break;

		case 'v2':
			// Modificar vecino
			$idVecinoModificar = $_POST['id'];
			$vecinos = Adop_VecinosController::index(['id' => $idVecinoModificar])[0];

			$data = [
				// 'nombre' => deutf8ize($_POST['nombre']),
				'nombre' => $_POST['nombre'],
				'dni' => $_POST['dni'],
				'email' => $_POST['email'],
				'email_alternativo' => $_POST['email_alternativo'],
				'telefono' => $_POST['telefono'],
				'telefono_alternativo' => $_POST['telefono_alternativo'],
				// 'ciudad' => deutf8ize($_POST['ciudad']),
				'ciudad' => $_POST['ciudad'],
				// 'domicilio' => deutf8ize($_POST['domicilio'])
				'domicilio' => $_POST['domicilio']
			];

			$id = Adop_VecinosController::update($data, $idVecinoModificar);

			if (!$id instanceof ErrorException) {
				$mensaje = "exito modificacion vecino";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			break;

		case 'v3':
			//! hacer que se deshabilite el vecino, no borrar
			// Eliminar vecino
			$idVecinoEliminar = $_POST['id'];

			$vecinosController = new Adop_VecinosController();
			$vecino = $vecinosController->delete($idVecinoEliminar);

			if (!$vecino instanceof ErrorException) {
				$mensaje = "Vecino eliminado correctamente";
			} else {
				sendRes(null, $vecino->getMessage(), null);
			};

			echo $mensaje;
			break;

		case 'ad1':
			// Cargar adopcion
			$adopciones = Adop_AdopcionesController::index();

			$data = [
				'nombre' => deutf8ize($_POST['nombre']),
				'dni' => $_POST['dni'],
				'email' => $_POST['email'],
				'email_alternativo' => $_POST['email_alternativo'],
				'telefono' => $_POST['telefono'],
				'telefono_alternativo' => $_POST['telefono_alternativo'],
				'ciudad' => deutf8ize($_POST['ciudad']),
				'domicilio' => deutf8ize($_POST['domicilio'])
			];

			$id = Adop_VecinosController::store($data);

			if (!$id instanceof ErrorException) {
				$mensaje = "exito carga vecino";
			} else {
				$mensaje = $id->getMessage();
				// $mensaje = "prueba error";
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
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

header("HTTP/1.1 200 Bad Request");

eClean();
