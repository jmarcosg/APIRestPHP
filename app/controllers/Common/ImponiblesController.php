<?php

namespace App\Controllers\Common;

use ErrorException;

class ImponiblesController
{
    public static function getImponiblesByDni($dni)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => BASE_WEB_LOGIN . "/Imponibles/$dni",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            if ($response) {
                return json_decode($response);
            } else {
                return new ErrorException('Problema con el inicio de sesion');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function getRodados($dni)
    {
        $data = self::getImponiblesByDni($dni);
        if ($data->value == 'NOT FOUND' || $data->error != null) {
            return false;
        }

        $rodados =  array_filter($data->value->imponibles, function ($imponible) {
            return $imponible->tipo == "(ROD) Patente de rodados" && $imponible->estado == "ACTIVO";
        });

        foreach ($rodados as $rodado) {
            $rodado->identificacion = str_replace("-", "", $rodado->identificacion);
        }

        if (count($rodados) == 0) {
            return false;
        }

        return $rodados;
    }
}
