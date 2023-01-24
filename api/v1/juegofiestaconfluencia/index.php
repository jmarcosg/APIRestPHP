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
				$data = MEMCONF_UsuarioController::index();

				if (count($data) == 0) {
					$data = [
						'usuario' => null,
						'error' => "No hay registros de usuarios"
					];
				} else {
					$data = [
						'usuario' => $data,
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
				//* Obtener configuracion activa
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

			case 'm1':
				//* Listado de partidas
				$data = MEMCONF_PartidaController::index();

				if (count($data) == 0) {
					$data = [
						'partida' => null,
						'error' => "No hay registros de usuarios"
					];
				} else {
					$data = [
						'partida' => $data,
						'error' => null,
					];
				}
				break;

			case 'm2':
				//* Listado de partidas ganadas
				$fechaSeleccionada = $_GET['fecha_seleccionada'];
				$data = MEMCONF_PartidaController::getGamesWon($fechaSeleccionada);

				if (count($data) == 0) {
					$data = [
						'usuario' => null,
						'error' => "No hay registros de usuarios ganadores"
					];
				} else {
					$data = [
						'usuario' => $data,
						'error' => null,
					];
				}
				break;

			case 'm3':
				//* Obtener partida por id
				$data = MEMCONF_PartidaController::index(['id' => $_GET['id']]);

				if (count($data) == 0) {
					$data = [
						'partida' => null,
						'error' => "No hay registros de esa partida"
					];
				} else {
					$data = [
						'partida' => $data,
						'error' => null,
					];
				}
				break;

			case 'vupt':
				//* Verificar si el usuario jugo en el dia
				$date = new DateTime('now');
				$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
				$fechaHoy = $date->format('Y-m-d');
				$usuario = MEMCONF_UsuarioController::index(['usuario_instagram' => $_GET['usuario_instagram']]);

				/**
				 * Indexo al usuario para verificar existencia
				 * Si existe, verifico si jugo el dia de la fecha
				 * De existir, avisa al usuario
				 * De lo contrario, retorna Ok
				 */
				if ($usuario) {
					// Verifico que el usuario a jugar no haya jugado en el dia de la fecha
					$partidaDistinta = MEMCONF_PartidaController::getUserIfUserHasPlayedToday($usuario[0]['id'], $fechaHoy);

					if (count($partidaDistinta) != 0) {
						sendRes(null, 'Este usuario ya jugó hoy');
					} else {
						sendRes(null, 'Ok');
					}
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
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaHoy = $date->format('Y-m-d');

			/**
			 * Indexo al usuario para verificar existencia
			 * Si no existe, lo carga como nuevo usuario
			 */
			$usuario = MEMCONF_UsuarioController::index(['usuario_instagram' => $_POST['usuario_instagram']]);

			if (!$usuario) {
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
			}

			sendRes($mensaje);
			exit;

		case 'm1':
			//* Cargar partida
			$partida = MEMCONF_PartidaController::index();
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaHoy = $date->format('Y-m-d H:i:s');

			$data = [
				'id_usuario' => $_POST['id_usuario'],
				'id_configuracion' => $_POST['id_configuracion'],
				'aciertos' => $_POST['aciertos'],
				'movimientos_totales' => $_POST['movimientos_totales'],
				'gano' => $_POST['gano'],
				'fecha_jugada' => $fechaHoy
			];

			$id = MEMCONF_PartidaController::store($data);

			if (!$id instanceof ErrorException) {
				$mensaje = "Exito al cargar partida";
			} else {
				$mensaje = $id->getMessage();
				logFileEE('prueba', $id, null, null);
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
			$configController = new MEMCONF_ConfiguracionController();
			$idConfiguracionActivar = $_POST['id'];
			$date = new DateTime('now');
			$date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
			$fechaActivacion = $date->format('Y-m-d H:i:s');

			$configDesactivar = $configController::index(['activa' => 1]);

			$dataDesactivar = [
				'activa' => 0,
				'fecha_activacion' => null
			];

			$configVieja = MEMCONF_ConfiguracionController::update($dataDesactivar, $configDesactivar[0]['id']);

			if (!$configVieja instanceof ErrorException) {
				$dataActivar = [
					'activa' => 1,
					'fecha_activacion' => $fechaActivacion
				];

				$configNueva = MEMCONF_ConfiguracionController::update($dataActivar, $idConfiguracionActivar);

				if (!$configNueva instanceof ErrorException) {
					$mensaje = "Configuracion habilitada correctamente";
				} else {
					sendRes(null, $configNueva->getMessage(), null);
				};
			} else {
				sendRes(null, $configVieja->getMessage(), null);
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
