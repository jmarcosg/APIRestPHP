<?php

namespace App\Controllers\TurneroTaxisCamara;

use App\Models\TurneroTaxisCamara\TCT_Fecha;
use DateTime;

date_default_timezone_set('America/Buenos_Aires');

class TCT_FechaController
{
    public static function seed()
    {
        $year = 2023;
        $days_in_year = 365;

        $fechaObj = new TCT_Fecha();
        $fechaObj->list()->value;
        if (count($fechaObj->value) > 0) {
            return null;
        } else {

            // Iterar sobre todos los días del año

            for ($day = 1; $day <= $days_in_year; $day++) {
                $fecha = date_create_from_format('z Y', ($day - 1) . ' ' . $year);

                // Excluir los sábados y domingos
                $fechaObj = new TCT_Fecha();
                if ($fecha->format('N') < 6) {
                    $codigo = $fecha->format('m-d');
                    $fechaObj->set([
                        'codigo' => $codigo
                    ]);
                    $fechaObj->save();
                }
            }
            return true;
        }
    }

    public static function getProximasFechas()
    {
        $fecha = new DateTime();
        $fecha = $fecha->format('m-d');
        $fechaObj = new TCT_Fecha();
        $fechaObj->list(["TOP" => 3, "codigo" => $fecha], ["codigo" => ">"])->value;
        $newArray = [];
        foreach ($fechaObj->value as $fecha) {
            $fechaExplode = explode("-", $fecha['codigo']);
            $newArray[] = ["id" => $fecha['id'], "codigo" => $fechaExplode[1] . '-' . $fechaExplode[0]];
        }
        return ["success" => $newArray, "error" => null];
    }
}
