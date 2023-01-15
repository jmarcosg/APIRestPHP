<?php

use App\Controllers\JuegoFiestaConfluencia\MEMCONF_UsuarioController;
use App\Controllers\JuegoFiestaConfluencia\MEMCONF_PartidaController;
use App\Controllers\JuegoFiestaConfluencia\MEMCONF_ConfiguracionController;

$dotenv = \Dotenv\Dotenv::createImmutable('./juegofiestaconfluencia/');
$dotenv->load();

include './juegofiestaconfluencia/config.php';

/**
 * *Metodo GET
 */

if ($url['method'] == "GET") {
	if (isset($_GET['action'])) {

		switch ($_GET['action']) {
			case 'u1':
				//* Obtener usuario con todos sus datos por usuario de instagram
				$animalesController = new MEMCONF_UsuarioController();
				$data = MEMCONF_UsuarioController::index(['usuario_instagram' => $_GET['usuario_instagram']]);

				if (count($data) == 0) {
					$data = [
						'usuario' => null,
						'error' => "Usuario no encontrado"
					];
				} else {
					$data = [
						'usuario' => $data[0],
						'error' => null,
					];
				}
				break;
			case 'u2':
				//* Listado de usuarios
				$animalesController = new MEMCONF_UsuarioController();
				$data = $MEMCONF_UsuarioController->index();

				if (count($data) == 0) {
					$data = [
						'usuario' => null,
						'error' => "No hay registros de usuarios"
					];
				} else {
					$data = [
						'usuario' => $data[0],
						'error' => null,
					];
				}
				break;
			case 'c1':
				//* Listado de configuraciones
				$data = MEMCONF_ConfiguracionController::index();

				if (count($data) == 0) {
					$data = [
						'configuracion' => null,
						'error' => "No hay configuraciones personalizadas cargadas"
					];
				} else {
					$data = [
						'configuracion' => $data,
					];
				}

				break;

			case 'c2':
				//* Listado de configuraciones
				$configController = new MEMCONF_ConfiguracionController();
				$data = $configController::index(['activa' => 1]);

				if (count($data) == 0) {
					$data = [
						'configuracion' => null,
						'error' => "No hay configuraciones activas"
					];
				} else {
					$data = [
						'configuracion' => $data,
					];
				}

				break;

			case 't':
				echo "hola get juego";
				exit;

			default:
				$error = new ErrorException('El action no es valido');
				sendRes(null, $error->getMessage(), $_GET);
				exit;
		}
	} else {
		$data = [
			'error' => "Imposible conseguir esos datos"
		];
	}

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
		case 'u1':
			//* Cargar usuario
			$partidas = MEMCONF_PartidaController::index();
			$usuarios = MEMCONF_PartidaController::index();
			$fechaModificado = $date->format('Y-m-d H:i:s');

			// Verifico que el usuario a jugar no haya jugado en el dia de la fecha
			$usuario = MEMCONF_UsuarioController::index(['usuario_instagram' => $_POST['usuario_instagram']]);
			$partidaDistinta = MEMCONF_PartidaController::index(['id_usuario' => $usuario[0]['id'], 'fecha_partida' => date('Y-m-d')]);

			if (count($partidaDistinta) == 0) {
				$data = [
					'usuario_instagram' => $_POST['usuario_instagram']
				];

				$id = MEMCONF_UsuarioController::store($data);

				if (!$id instanceof ErrorException) {
					$mensaje = "Exito carga usuario";
				} else {
					$mensaje = $id->getMessage();
					// $mensaje = "prueba error";
					logFileEE('prueba', $id, null, null);
				}
			} else {
				$mensaje = "Este usuario ya jugo hoy";
			}

			sendRes($mensaje);
			exit;

		case 'c1':
			//* Cargar configuracion
			$configuraciones = MEMCONF_ConfiguracionController::index();

			// Verifico que la config a cargar no exista en la bd
			$configDistinta = MEMCONF_ConfiguracionController::index(['descripcion' => $_POST['descripcion']]);

			if (count($configDistinta) == 0) {
				$data = [
					'descripcion' => $_POST['descripcion'],
					'tiempo' => $_POST['tiempo'],
					'activa' => 0,
					'fecha_activacion' => null
				];

				$id = MEMCONF_ConfiguracionController::store($data);

				if (!$id instanceof ErrorException) {
					$mensaje = "Exito carga configuracion";
				} else {
					$mensaje = $id->getMessage();
					// $mensaje = "prueba error";
					logFileEE('prueba', $id, null, null);
				}
			} else {
				$mensaje = "Configuracion ya cargada";
			}

			sendRes($mensaje);
			exit;

		case 'c2':
			//* Activar configuracion
			$idConfiguracionActivar = MEMCONF_ConfiguracionController::index(['id' => $_POST['id']])[0];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaActivacion = $date->format('Y-m-d H:i:s');

			$data = [
				'activa' => 1,
				'fecha_activacion' => $fechaActivacion
			];

			$configuracionController = new MEMCONF_ConfiguracionController();
			$config = MEMCONF_ConfiguracionController::update($data, $idConfiguracionActivar);

			if (!$config instanceof ErrorException) {
				$mensaje = "Configuracion habilitada correctamente";
			} else {
				sendRes(null, $config->getMessage(), null);
			};

			sendRes($mensaje);
			exit;

		case 't':
			echo "test post juego";
			exit;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
