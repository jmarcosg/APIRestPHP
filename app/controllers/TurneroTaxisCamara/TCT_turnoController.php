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

        if ($turnosDisponibles > 0)
            $data = [
                "manana" => $turnosManana,
                "tarde" => $turnosTarde,
                "turnosDisponibles" => $turnosDisponibles
            ];


        return ["success" => $data, "error" => $data != null ? null : "No hay turnos disponibles"];
    }

    public static function store($req)
    {
        $turnosPersona = new TCT_Turno();
        $turnosPersona->list(["licencia" => $req['licencia']]);
        if (count($turnosPersona->value) > 0 && count($turnosPersona->value) < 5) {
            $turnoUpdate = new TCT_Turno();
            $turnoUpdate->update($req, $turnosPersona->value[0]['id']);

            if ($turnoUpdate)
                return ["success" => true, "error" => null];

            return ["success" => null, "error" => "Error en la actualización del turno"];
        }

        $turnosGuardados = new TCT_Turno();
        $turnosGuardados->list(["fecha_id" => $req['fecha_id'], "turno" => $req['turno']]);
        if (count($turnosGuardados->value) >= 8)
            return ["success" => null, "error" => "Ya no hay turnos disponibles"];


        $req['verificado'] = 0;
        $turnoObj = new TCT_Turno();
        $turnoObj->set($req);
        $turnoObj->save();

        if ($turnoObj)
            return ["success" => true, "error" => null];


        return ["success" => null, "error" => "Error al guardar el turno"];
    }

    public static function delete($req)
    {
        $turnoPersona = new TCT_Turno();
        $turnosPersona = $turnoPersona->list(["usuario_id" => $req['usuario_id']])->value;
        $turnoObj = new TCT_Turno();
        $turnoObj->delete($turnosPersona[0]['id']);

        if ($turnoObj)
            return ["success" => true, "error" => null];


        return ["success" => null, "error" => "Error al eliminar el turno"];
    }

    public static function getTurnosUsuario($req)
    {
        $turnoPersona = new TCT_Turno();
        $turnosPersona = $turnoPersona->list(["usuario_id" => $req])->value;

        if (count($turnosPersona) > 0) {
            $data = [];
            foreach ($turnosPersona as $turno) {
                $fechaObj = new TCT_Fecha();
                $fechaTurno = $fechaObj->list(["id" => $turno['fecha_id']])->value;

                $fechaExplode = explode("-", $fechaTurno[0]['codigo']);
                $fechaTurno = $fechaExplode[1] . '-' . $fechaExplode[0];

                $turno['fecha'] = $fechaTurno;
                unset($turno['fecha_id']);
                $data[] = $turno;
            }
            return ["success" => $data, "error" => null];
        }

        return ["success" => null, "error" => "No se encontró el turno"];
    }

    public static function getTurnosAdmin()
    {
        $turnoObj = new TCT_Turno();
        $turnos = $turnoObj->list()->value;
        $data = [];
        foreach ($turnos as $turno) {
            $fecha = new TCT_Fecha();
            $fechaTurno = $fecha->list(["id" => $turno['fecha_id']])->value[0];
            $turno['fecha'] = $fechaTurno['codigo'];
            unset($turno['fecha_id']);
            $data[] = $turno;
        }

        return ["success" => $data, "error" => null];
    }

    public static function verificarTurno($req)
    {
        $turnoObj = new TCT_Turno();
        $turno = $turnoObj->list(["id" => $req['id']])->value[0];

        $turno['verificado'] = $req['verificado'];
        $turnoObj->update($turno, $req['id']);

        return ["success" => true, "error" => null];
    }
}
