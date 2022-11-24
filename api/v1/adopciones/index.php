<?php

use App\Controllers\Adopciones\Adop_AdopcionesController;
use App\Controllers\Adopciones\Adop_AdoptantesController;
use App\Controllers\Adopciones\Adop_AnimalesController;
use App\Controllers\Adopciones\Adop_EmpleadosController;

$dotenv = \Dotenv\Dotenv::createImmutable('./adopciones/');
$dotenv->load();

include './adopciones/config.php';

/**
 * *Metodo GET
 */

if ($url['method'] == "GET") {
	if (isset($_GET['action'])) {

		switch ($_GET['action']) {
			case 'an1':
				//* Listado simple de animales
				$animalesController = new Adop_AnimalesController();
				$data = $animalesController->index();

				$data = [
					'data' => $data,
				];

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
				//* Obtener animal con todos sus datos por id
				$animalesController = new Adop_AnimalesController();
				$data = Adop_AnimalesController::indexEverything(['id' => $_GET['id']]);

				if (count($data) == 0) {
					$data = [
						'animal' => null,
						'error' => "Animal no encontrado"
					];
				} else {
					$data = $data[0];
				}
				break;

			case 'an3':
				//* Obtener fotos del animal por id
				$animalesController = new Adop_AnimalesController();
				$data = Adop_AnimalesController::indexAnimalPhotos(['id' => $_GET['id']]);

				if (count($data) == 0) {
					$data = [
						'animal' => null,
						'error' => "Animal no encontrado"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 'v1':
				//* Listado de adoptantes
				$data = Adop_AdoptantesController::index();

				$data = [
					'data' => $data,
				];

				if (count($data) == 0) {
					$data = [
						'adoptante' => null,
						'error' => "No hay registros de adoptantes"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 'v2':
				//* Obtener adoptante por dni
				$adoptantesController = new Adop_AdoptantesController();
				$data = Adop_AdoptantesController::index(['dni' => $_GET['dni']]);

				$data = [
					'data' => $data,
				];

				if (count($data['data']) == 0) {
					$data = [
						'adoptante' => null,
						'error' => "Adoptante no encontrado"
					];
				} else {
					$data = $data['data'][0];
				}
				break;

			case 'v3':
				//* Obtener adoptante por id animal
				$adopcionesController = new Adop_AdopcionesController();
				$adopciones = Adop_AdopcionesController::index(['id_animal' => $_GET['id_animal']]);

				// filtro por la adopcion mas reciente
				$adopcionMasReciente = $adopciones[count($adopciones) - 1];

				// obtengo el adoptante de la adopcion mas reciente
				$data = Adop_AdoptantesController::index(['id' => $adopcionMasReciente['id_adoptante']])[0];


				if (count($data) == 0) {
					$data = [
						'adopciones' => null,
						'error' => "Éste animal todavía no fue adoptado"
					];
				}
				break;

			case 'ad1':
				//* Listado de adopciones
				$data = Adop_AdopcionesController::index();

				$data = [
					'data' => $data,
				];

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
				//* Obtener adopciones por id adoptante
				$adopcionesController = new Adop_AdopcionesController();
				$adopciones = Adop_AdopcionesController::getMostRecentAdopterAdoptions($_GET['id_adoptante']);

				$data = [];
				foreach ($adopciones as $adopcion) {
					$animal = Adop_AnimalesController::indexEverything(['id' => $adopcion['id_animal']])[0];
					// formateo la fecha de adopcion con formato dd/mm/yyyy hh:mm
					$adopcion['fecha_adopcion'] = date('d/m/Y H:i', strtotime($adopcion['fecha_adopcion']));
					$animal['fecha_adopcion'] = $adopcion['fecha_adopcion'];
					array_push($data, $animal);
				}

				if (count($data) == 0) {
					$data = [
						'adopciones' => null,
						'error' => "Ésta persona no tiene adopciones generadas y asignadas"
					];
				}
				break;

			case 'e1':
				//* Listado de empleados
				$data = Adop_EmpleadosController::index();

				$data = [
					'data' => $data,
				];

				if (count($data) == 0) {
					$data = [
						'empleado' => null,
						'error' => "No hay registros de empleados"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case 'e2':
				//* Obtener empleado por id
				$empleadosController = new Adop_EmpleadosController();
				$data = Adop_EmpleadosController::index(['id' => $_GET['id']]);

				$data = [
					'data' => $data,
				];

				if (count($data) == 0) {
					$data = [
						'empleado' => null,
						'error' => "Empleado no encontrado"
					];
				} else {
					$data = $data[0];
					$data['error'] = null;
				}
				break;

			case 'e3':
				//* Obtener empleado por email
				$empleadosController = new Adop_EmpleadosController();
				$data = Adop_EmpleadosController::index(['email' => $_GET['email']]);

				if (count($data) == 0) {
					$data = [
						'empleado' => null,
						'error' => "Empleado no encontrado"
					];
				} else {
					$data['data'] = $data[0];
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

	// echo json_encode($data);
	if (!$data instanceof ErrorException) {
		sendRes($data);
	} else {
		sendRes(null, "No se encuentra el registro buscado");
	}
	exit;
}

/**
 * *Metodo POST
 */

if ($url['method'] == "POST") {
	switch ($_POST['action']) {
		case 'an1':
			//* Cargar animal
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$currentTime = $date->format('Y-m-d H:i:s');

			$data = [
				'imagen1_path' => "a",
				'imagen2_path' => "a",
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['edad'],
				'tipo_edad' => $_POST['tipo_edad'],
				'es_de_raza' => $_POST['es_de_raza'],
				'tipo_raza' => $_POST['tipo_raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'adoptado' => $_POST['adoptado'],
				'fecha_ingreso' => $currentTime,
				'fecha_modificacion' => null,
			];

			if ($_POST['tipo_raza'] != "otro" && $_POST['raza'] == "") {
				$data['raza'] = $_POST['tipo_raza'];
			} else if ($_POST['es_de_raza'] == "1") {
				$data['raza'] = $_POST['raza'];
			} else {
				$data['raza'] = $_POST['raza'];
			}

			$id = Adop_AnimalesController::store($data);

			if (!$id instanceof ErrorException) {
				$imagenCargada = Adop_AnimalesController::storeImage($_FILES['imagen1_path'], $id, "imagen1_path");
				if ($imagenCargada) {
					$imagenCargada = Adop_AnimalesController::storeImage($_FILES['imagen2_path'], $id, "imagen2_path");
				}

				$animal = Adop_AnimalesController::index(['id' => $id])[0];

				sendRes($animal);
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			exit;


		case 'an2':
			//* Modificar animal
			$idAnimalModificar = $_POST['id'];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaModificado = $date->format('Y-m-d H:i:s');

			$data = [
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['edad'],
				'tipo_edad' => $_POST['tipo_edad'],
				'es_de_raza' => $_POST['es_de_raza'],
				'tipo_raza' => $_POST['tipo_raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion'],
				'fecha_modificacion' => $fechaModificado,
			];

			if ($_POST['tipo_raza'] != "otro" && $_POST['raza'] == "") {
				$data['raza'] = $_POST['tipo_raza'];
			} else if ($_POST['es_de_raza'] == "1") {
				$data['raza'] = $_POST['raza'];
			} else {
				$data['raza'] = $_POST['raza'];
			}

			$id = Adop_AnimalesController::update($data, $idAnimalModificar);

			if (!$id instanceof ErrorException) {
				if (isset($_FILES['imagen1_path'])) {
					$imagen1Modificada = Adop_AnimalesController::storeImage($_FILES['imagen1_path'], $idAnimalModificar, "imagen1_path");
				}

				if (isset($_FILES['imagen2_path'])) {
					$imagen2Modificada = Adop_AnimalesController::storeImage($_FILES['imagen2_path'], $idAnimalModificar, "imagen2_path");
				}
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			exit;

		case 'an3':
			//* Eliminar animal
			$idAnimalEliminar = $_POST['id'];

			$animalesController = new Adop_AnimalesController();
			$animal = $animalesController->delete($idAnimalEliminar);

			if (!$animal instanceof ErrorException) {
				$mensaje = "Animal eliminado correctamente";
			} else {
				sendRes(null, $animal->getMessage(), null);
			};

			echo $mensaje;
			exit;

		case 'v1':
			//* Cargar adoptante
			$adoptantes = Adop_AdoptantesController::index();

			// Verifico que el adoptante a cargar no exista en la bd
			$adoptanteDistinto = Adop_AdoptantesController::index(['dni' => $_POST['dni']]);

			if (count($adoptanteDistinto) == 0) {
				$data = [
					'nombre' => $_POST['nombre'],
					'dni' => $_POST['dni'],
					'email' => $_POST['email'],
					'email_alternativo' => $_POST['email_alternativo'],
					'telefono' => $_POST['telefono'],
					'telefono_alternativo' => $_POST['telefono_alternativo'],
					'ciudad' => $_POST['ciudad'],
					'domicilio' => $_POST['domicilio'],
					'deshabilitado' => 0,
					'fecha_deshabilitado' => null,
				];

				$id = Adop_AdoptantesController::store($data);

				if (!$id instanceof ErrorException) {
					$mensaje = "exito carga adoptante";
					sendRes($mensaje);
				} else {
					$mensaje = $id->getMessage();
					logFileEE('prueba', $id, null, null);
					sendRes($mensaje);
				}
			} else {
				$mensaje = "Adoptante ya existente";
				sendRes($mensaje);
			}

			exit;

		case 'v2':
			//* Modificar adoptante
			$dniAdoptanteModificar = $_POST['dni'];
			$adoptante = Adop_AdoptantesController::index(['dni' => $dniAdoptanteModificar])[0];

			$data = [
				'email' => $_POST['email'],
				'email_alternativo' => $_POST['email_alternativo'],
				'telefono' => $_POST['telefono'],
				'telefono_alternativo' => $_POST['telefono_alternativo'],
				'domicilio' => $_POST['domicilio']
			];

			$id = Adop_AdoptantesController::update($data, $adoptante['id']);

			if (!$id instanceof ErrorException) {
				$mensaje = "exito modificacion adoptante";
				sendRes($mensaje);
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
				sendRes($mensaje);
			}

			echo $mensaje;
			exit;
		case 'v3':
			//* Deshabilitar adoptante
			$idAdoptanteDeshabilitar = $_POST['id'];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaDeshabilitado = $date->format('Y-m-d H:i:s');

			$data = [
				'deshabilitado' => 1,
				'fecha_deshabilitado' => $fechaDeshabilitado
			];

			$adoptantesController = new Adop_AdoptantesController();
			$adoptante = Adop_AdoptantesController::update($data, $idAdoptanteDeshabilitar);

			if (!$adoptante instanceof ErrorException) {
				$mensaje = "Adoptante deshabilitado correctamente";
			} else {
				sendRes(null, $adoptante->getMessage(), null);
			};

			echo $mensaje;
			exit;

		case 'v4':
			//* Habilitar adoptante
			$idAdoptanteHabilitar = $_POST['id'];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaHabilitado = $date->format('Y-m-d H:i:s');

			$data = [
				'deshabilitado' => 0,
				'fecha_deshabilitado' => null
			];

			$adoptantesController = new Adop_AdoptantesController();
			$adoptante = Adop_AdoptantesController::update($data, $idAdoptanteHabilitar);

			if (!$adoptante instanceof ErrorException) {
				$mensaje = "Adoptante habilitado correctamente";
			} else {
				sendRes(null, $adoptante->getMessage(), null);
			};

			echo $mensaje;
			exit;
		case 'vdel':
			//* Eliminar adoptante
			$idAdoptanteEliminar = $_POST['id'];

			$adoptantesController = new Adop_AdoptantesController();
			$adoptante = $adoptantesController->delete($idAdoptanteEliminar);

			if (!$adoptante instanceof ErrorException) {
				$mensaje = "Adoptante eliminado correctamente";
			} else {
				sendRes(null, $adoptante->getMessage(), null);
			};

			echo $mensaje;
			exit;

		case 'ad1':
			//* Cargar adopcion
			$adopciones = Adop_AdopcionesController::index();
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$currentTime = $date->format('Y-m-d H:i:s');

			$dataAnimal = [
				'adoptado' => 1,
				'fecha_modificacion' =>	$currentTime
			];

			$dataAdopcion = [
				'id_empleado' => $_POST['id_empleado'],
				'id_adoptante' => $_POST['id_adoptante'],
				'id_animal' => $_POST['id_animal'],
				'fecha_adopcion' => $currentTime
			];

			$animalAdoptado = Adop_AnimalesController::update($dataAnimal, $_POST['id_animal']);

			if (!$animalAdoptado instanceof ErrorException) {
				$adopcionGenerada = Adop_AdopcionesController::store($dataAdopcion);
				if (!$adopcionGenerada instanceof ErrorException) {
					$mensaje = "exito adopcion";
					sendRes($mensaje);
				} else {
					$mensaje = $id->getMessage();
					logFileEE('prueba', $id, null, null);
				}
			} else {
				$mensaje = "error adopcion";
				logFileEE('prueba', $animalAdoptado, null, null);
				sendRes($mensaje);
			}

			exit;

		case 'ad2':
			//* Desadopcion(?)
			$adopciones = Adop_AdopcionesController::index();
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaModificado = $date->format('Y-m-d H:i:s');

			$dataAnimal = [
				'adoptado' => 0,
				'fecha_modificacion' =>	$fechaModificado
			];

			$animalAdoptado = Adop_AnimalesController::update($dataAnimal, $_POST['id_animal']);

			if (!$animalAdoptado instanceof ErrorException) {
				$mensaje = "exito desadopcion";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			exit;

		case 'e1':
			//* Cargar empleado
			$empleados = Adop_EmpleadosController::index();

			// Verifico que el adoptante a cargar no exista en la bd
			$empleadoDistinto = Adop_EmpleadosController::index(['dni' => $_POST['dni']]);

			if (count($empleadoDistinto) == 0) {
				$data = [
					'nombre' => $_POST['nombre'],
					'dni' => $_POST['dni'],
					'email' => $_POST['email'],
					'telefono' => $_POST['telefono'],
					'ciudad' => $_POST['ciudad'],
					'domicilio' => $_POST['domicilio'],
					'deshabilitado' => 0,
					'fecha_deshabilitado' => null,
				];

				$id = Adop_EmpleadosController::store($data);

				if (!$id instanceof ErrorException) {
					$mensaje = "exito carga empleado";
				} else {
					$mensaje = $id->getMessage();
					// $mensaje = "prueba error";
					logFileEE('prueba', $id, null, null);
				}
			} else {
				$mensaje = "empleado ya cargado";
			}

			echo $mensaje;
			exit;

		case 'e2':
			//* Modificar empleado
			$idEmpleadoModificar = $_POST['id'];
			$empleados = Adop_EmpleadosController::index(['id' => $idEmpleadoModificar])[0];

			$data = [
				'nombre' => $_POST['nombre'],
				'dni' => $_POST['dni'],
				'email' => $_POST['email'],
				'email_alternativo' => $_POST['email_alternativo'],
				'telefono' => $_POST['telefono'],
				'telefono_alternativo' => $_POST['telefono_alternativo'],
				'ciudad' => $_POST['ciudad'],
				'domicilio' => $_POST['domicilio']
			];

			$id = Adop_EmpleadosController::update($data, $idEmpleadoModificar);

			if (!$id instanceof ErrorException) {
				$mensaje = "exito modificacion adoptante";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
			}

			echo $mensaje;
			exit;
		case 'v3':
			//* Deshabilitar empleado
			$idEmpleadoDeshabilitar = $_POST['id'];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaDeshabilitado = $date->format('Y-m-d H:i:s');

			$data = [
				'deshabilitado' => 1,
				'fecha_deshabilitado' => $fechaDeshabilitado
			];

			$empleadosController = new Adop_EmpleadosController();
			$empleado = Adop_EmpleadosController::update($data, $idEmpleadoDeshabilitar);

			if (!$empleado instanceof ErrorException) {
				$mensaje = "Empleado deshabilitado correctamente";
			} else {
				sendRes(null, $empleado->getMessage(), null);
			};

			echo $mensaje;
			exit;

		case 'v4':
			//* Habilitar empleado
			$idEmpleadoHabilitar = $_POST['id'];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaHabilitado = $date->format('Y-m-d H:i:s');

			$data = [
				'deshabilitado' => 0,
				'fecha_deshabilitado' => null
			];

			$empleadosController = new Adop_EmpleadosController();
			$empleado = Adop_EmpleadosController::update($data, $idEmpleadoHabilitar);

			if (!$empleado instanceof ErrorException) {
				$mensaje = "Empleado habilitado correctamente";
			} else {
				sendRes(null, $empleado->getMessage(), null);
			};

			echo $mensaje;
			exit;
		case 'edel':
			//* Eliminar empleado
			$idEmpleadoEliminar = $_POST['id'];

			$empleadosController = new Adop_EmpleadosController();
			$empleado = $empleadosController->delete($idEmpleadoEliminar);

			if (!$empleado instanceof ErrorException) {
				$mensaje = "Empleado eliminado correctamente";
			} else {
				sendRes(null, $empleado->getMessage(), null);
			};

			echo $mensaje;
			exit;

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
