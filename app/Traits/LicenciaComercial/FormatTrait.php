<?php

namespace App\Traits\LicenciaComercial;

use App\Controllers\RenaperController;

trait FormatTrait
{
    public static function formatEsTerceroSolicitud($data)
    {
        if ($data['pertenece'] == 'tercero') {
            $rc = new RenaperController();
            $dni = $data["dni_tercero"];
            $tramite = $data["tramite_tercero"];
            $genero = $data["genero_tercero"];
            $data['dataTercero'] = $rc->getDataTramite($genero, $dni, $tramite);
        }

        return $data;
    }
}
