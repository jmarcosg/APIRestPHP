<?php

namespace App\Traits\WebLogin;

use App\Controllers\Weblogin\QueryData;
use ErrorException;

trait GettersDataTrait
{
    use QueryData;
    /** Obtenemos los datos del legajo en funcion del sexo y documento */
    private static function getLegajoData($genero, $dni)
    {
        $legajo = self::datosLegajo($genero, $dni);

        $legajo = self::formatData($legajo, 'Problame al obtener el legajo');
        return $legajo;
    }

    /** Obtenemos los datos del acarreo */
    private static function getAcarreoData($id_usuario)
    {
        /* Arreglo de datos */
        $acarreo = self::datosAccareo($id_usuario);

        $acarreo = self::formatData($acarreo, 'Problame al obtener el acarreo');
        return $acarreo;
    }

    /** Obtenemos los datos de licencia de conducir */
    private static function getLicConducirData($dni)
    {
        $licencia = self::datosLicConducir($dni);

        $licencia = self::formatData($licencia, 'Problame al obtener la licencia de conducir');
        return $licencia;
    }

    /** Obtenemos los datos de de MuniEventos */
    private static function getMuniEventos($dni)
    {
        $muniEventos = self::getMuniEventosFetch($dni);

        $muniEventos = self::formatData($muniEventos, 'Problame al obtener los eventos');
        return $muniEventos;
    }

    /** Obtenemos los datos de Licencia Comercial */
    private static function getLicenciaComercial($id_usuario)
    {
        $licComercial = self::datosLicComercial($id_usuario);

        if ($licComercial && !$licComercial instanceof ErrorException) {
            usort($licComercial, function ($a, $b) {
                return intval($a['historial']) < intval($b['historial']);
            });
        }

        $licComercial = self::formatData($licComercial, 'Problema al obtener la licencia comercial', 'No se encontraron licencias comerciales');
        return $licComercial;
    }

    /** Obtenemos los datos de Carnet de manipulacion */
    private static function getLibretasanitariaData($id_usuario)
    {
        $libretaSanitaria = self::datosLibretaSanitaria($id_usuario);

        $libretaSanitaria = self::formatData($libretaSanitaria, 'Problame al obtener carnet de manipulacion');
        return $libretaSanitaria;
    }

    /** Obtenemos los datos de Carnet de manipulacion */
    private static function getLibretasanitariaDataDos()
    {
        $libretaSanitaria = self::datosLibretaSanitaria(37216);

        $libretaSanitaria = self::formatData($libretaSanitaria, 'Problame al obtener carnet de manipulacion');
        return $libretaSanitaria;
    }
}
