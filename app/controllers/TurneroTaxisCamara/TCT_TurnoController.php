<?php

namespace App\Controllers\TurneroTaxisCamara;

use App\Models\TurneroTaxisCamara\TCT_Turno;

class TCT_TurnoController
{
    public static function getTurnos($fecha_id)
    {
        $turnoMananaObj = new TCT_Turno();
        $turnoTardeObj = new TCT_Turno();

        $turnosManana = 8;
        $turnosTarde = 8;

        $turnoMananaObj->list(["fecha_id" => $fecha_id, "turno" => "M"]);
        $turnoTardeObj->list(["fecha_id" => $fecha_id, "turno" => "T"]);

        $turnosManana -= count($turnoMananaObj->value);
        $turnosTarde -= count($turnoTardeObj->value);

        $turnosDisponibles = $turnosManana + $turnosTarde;

        $data = null;

        if ($turnosDisponibles > 0) {
            $data = [
                "manana" => $turnosManana,
                "tarde" => $turnosTarde,
                "turnosDisponibles" => $turnosDisponibles
            ];
        }

        return $data;
    }
}
