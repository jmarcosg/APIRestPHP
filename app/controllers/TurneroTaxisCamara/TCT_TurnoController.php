<?php

namespace App\Controllers\TurneroTaxisCamara;

use App\Models\TurneroTaxisCamara\TCT_Fecha;
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

        return ["success" => $data, "error" => $data != null ? null : "No hay turnos disponibles"];
    }

    public static function store($req)
    {
        $turnosPersona = new TCT_Turno();
        $turnosPersona->list(["usuario_id" => $req['usuario_id']]);
        if (count($turnosPersona->value) > 0) {
            $turnoUpdate = new TCT_Turno();
            $turnoUpdate->update($req, $turnosPersona->value[0]['id']);

            if ($turnoUpdate)
                return ["success" => true, "error" => null];

            return ["success" => null, "error" => "Error en la actualización del turno"];
        }

        $turnosGuardados = new TCT_Turno();
        $turnosGuardados->list(["fecha_id" => $req['fecha_id'], "turno" => $req['turno']]);
        if (count($turnosGuardados->value) >= 8) {
            return ["success" => null, "error" => "Ya no hay turnos disponibles"];
        }

        $turnoObj = new TCT_Turno();
        $turnoObj->set($req);
        $turnoObj->save();

        if ($turnoObj) {
            return ["success" => true, "error" => null];
        }

        return ["success" => null, "error" => "Error al guardar el turno"];
    }

    public static function delete($req)
    {
        $turnoPersona = new TCT_Turno();
        $turnosPersona = $turnoPersona->list(["usuario_id" => $req['usuario_id']])->value;
        $turnoObj = new TCT_Turno();
        $turnoObj->delete($turnosPersona[0]['id']);

        if ($turnoObj) {
            return ["success" => true, "error" => null];
        }

        return ["success" => null, "error" => "Error al eliminar el turno"];
    }
}
