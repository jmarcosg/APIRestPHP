<?php
$noUrl = true;

include "../../../app/config/paths.php";

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

$dotenv = \Dotenv\Dotenv::createImmutable(V1_PATH . '/adopciones');
$dotenv->load();

// $dotenv = \Dotenv\Dotenv::createImmutable("./");
// $dotenv->load();

include './config.php';

use App\Controllers\JuegoFiestaConfluencia\MEMCONF_UsuarioController;
use App\Controllers\JuegoFiestaConfluencia\MEMCONF_PartidaController;
use App\Controllers\JuegoFiestaConfluencia\MEMCONF_ConfiguracionController;

if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    switch ($_GET['action']) {
        case 'c1':
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


            if (!$data instanceof ErrorException) {
                sendRes($data);
            } else {
                sendRes(null, "No se encuentra el registro buscado");
            }
            exit;

        case 'vupt':
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
                    $data = [
                        'usuario' => $usuario,
                        'habilitado' => false
                    ];
                } else {
                    $data = [
                        'usuario' => $usuario,
                        'habilitado' => true
                    ];
                }
            } else {
                $data = [
                    'habilitado' => true
                ];
            }


            if (!$data instanceof ErrorException) {
                sendRes($data);
            } else {
                sendRes(null, "No se encuentra el registro buscado");
            }
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

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
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
             * Si existe, verifico si jugo el dia de la fecha
             * De lo contrario, lo carga como nuevo usuario
             */
            $usuario = MEMCONF_UsuarioController::index(['usuario_instagram' => $_POST['usuario_instagram']]);

            if ($usuario) {
                // Verifico que el usuario a jugar no haya jugado en el dia de la fecha
                $partidaDistinta = MEMCONF_PartidaController::getUserIfUserHasPlayedToday($usuario[0]['id'], $fechaHoy);

                if (count($partidaDistinta) != 0) {
                    $mensaje = "Este usuario ya jugo hoy";
                }
            } else {
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

        case 't':
            echo "test post juego";
            exit;

        default:
            $error = new ErrorException('El action no es valido');
            sendRes(null, $error->getMessage(), $_GET);
            exit;
    }
}

exit;
