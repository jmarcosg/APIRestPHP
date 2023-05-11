<?php

use App\Controllers\TurneroTaxisCamara\TCT_FechaController;
use App\Controllers\TurneroTaxisCamara\TCT_TurnoController;

$dotenv = \Dotenv\Dotenv::createImmutable(('./credencialesempleados'));
$dotenv->load();

include './credencialesempleados/config.php';

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
        }

        sendRes($data, $data != null ? null : "Error en la obtención de los datos");
    }
}
