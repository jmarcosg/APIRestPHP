<?php

use App\Controllers\TurneroTaxisCamara\TCT_FechaController;
use App\Controllers\TurneroTaxisCamara\TCT_TurnoController;

$dotenv = \Dotenv\Dotenv::createImmutable(('./turnero-taxis-camara'));
$dotenv->load();

include './turnero-taxis-camara/config.php';

if ($url['method'] == "GET") {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        unset($_GET['action']);

        switch ($action) {
            case "seed":
                $data = TCT_FechaController::seed();
                break;

            case "fechas":
                $data = TCT_FechaController::getProximasFechas();
                break;

            case "turnos":
                $data = TCT_TurnoController::getTurnos($_GET['fecha_id']);
                break;

            default:
                sendRes(null, "Acción inválida");
                break;
        }

        sendRes($data["success"], $data["error"]);
    }
}

if ($url['method'] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        unset($_POST['action']);

        switch ($action) {
            case "store":
                $data = TCT_TurnoController::store($_POST);
                break;

            case "delete":
                $data = TCT_TurnoController::delete($_POST);
                break;

            default:
                sendRes(null, "Acción inválida");
                break;
        }

        sendRes($data["success"], $data["error"]);
    }
}

sendRes(null, "Método inválido");
