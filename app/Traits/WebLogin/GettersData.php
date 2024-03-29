<?php

namespace App\Traits\WebLogin;

use App\Traits\WebLogin\QueryData;
use DateTime;
use ErrorException;

trait GettersData
{
    use QueryData, Format;
    /** Obtenemos los datos del legajo en funcion del sexo y documento */
    private static function getLegajoData($genero, $dni)
    {
        $legajo = self::datosLegajo($genero, $dni);

        $legajo = self::formatDataWithError($legajo, 'Problame al obtener el legajo');
        return $legajo;
    }

    /** Obtenemos los datos del acarreo */
    private static function getAcarreoData($id_usuario)
    {
        /* Arreglo de datos */
        $acarreo = self::datosAccareo($id_usuario);

        $acarreo = self::formatDataWithError($acarreo, 'Problame al obtener el acarreo');
        return $acarreo;
    }

    /** Obtenemos los datos de licencia de conducir */
    private static function getLicConducirData($dni)
    {
        $licencia = self::datosLicConducir($dni);

        $licencia = self::formatLicenciaConducir($licencia);

        $licencia = self::formatDataWithError($licencia, 'Problame al obtener la licencia de conducir');
        return $licencia;
    }

    private static function formatLicenciaConducir($licencia)
    {
        $hoy = new DateTime();
        $vencimiento = new DateTime($licencia["venc"]);
        $diff = $hoy->diff($vencimiento)->days;
        $vigente = $vencimiento > $hoy;

        $licencia['show_init_tramite'] = false;
        if ($vigente) {
            $licencia['estado'] = 'Licencia Vigente';
            $licencia['show_renovacion_b'] = false;

            if ($diff <= 7) $licencia['show_init_tramite'] = true;
        } elseif (!$vigente) {
            $licencia['estado'] = 'Licencia Vencida';
            $licencia['show_renovacion_b'] = true;
            $licencia['show_init_tramite'] = true;
        }

        $licencia['dif'] = $diff;
        $licencia['vigente'] = $vigente;
        $licencia['donante'] = !!$licencia['donante'];

        return $licencia;
    }

    /** Obtenemos los datos de de MuniEventos */
    private static function getMuniEventos($dni)
    {
        $muniEventos = self::getMuniEventosFetch($dni);

        $muniEventos = self::formatDataWithError($muniEventos, 'Problame al obtener los eventos');
        return $muniEventos;
    }

    /** Obtenemos los datos de Licencia Comercial */
    private static function getLicenciaComercial($id_usuario)
    {
        $licComercial = self::datosLicComercial("id_usuario = $id_usuario", false);

        $data = self::filterLicComercial($licComercial);

        $licComercial = self::formatDataWithError($data, 'Problema al obtener la licencia comercial', 'No se encontraron licencias comerciales');
        return $licComercial;
    }

    private static function filterLicComercial($licComercial)
    {
        if ($licComercial && !$licComercial instanceof ErrorException) {
            $count = count($licComercial);
            if ($count > 0) {

                /* usort($licComercial, function ($a, $b) {
                    return intval($a['cant_historial']) < intval($b['cant_historial']);
                }); */

                $rechazadas = array_values(array_filter($licComercial, function ($sol) {
                    return str_contains($sol['estado'], 'rechazado');
                }));

                $retornados = array_values(array_filter($licComercial, function ($sol) {
                    return str_contains($sol['estado'], 'retornado');
                }));

                $finalizados = array_values(array_filter($licComercial, function ($sol) {
                    return $sol['estado'] == 'finalizado';
                }));

                $noFinalizados = array_values(array_filter($licComercial, function ($sol) {
                    return $sol['estado'] != 'finalizado' && !str_contains($sol['estado'], 'rechazado');
                }));

                $historial = array_values(array_filter($licComercial, function ($sol) {
                    return $sol['cant_historial'] > 0;
                }));

                $licComercial = [
                    'count' => $count,

                    /* Todas las solicitudes rechazadas */
                    'rechazadas' => count($rechazadas) > 0 ? $rechazadas : null,

                    /* Todas las solicitudes retornadas */
                    'retornados' => count($retornados) > 0 ? $retornados : null,

                    /* Todas las solicitudes finalizadas */
                    'finalizados' => count($finalizados) > 0 ? $finalizados : null,

                    /* Todas las solicitudes no finalizadas */
                    'noFinalizados' => count($noFinalizados) > 0 ? $noFinalizados : null,

                    /* Todas las solicitudes que tienen al menos una notificacion no visto */
                    'historial' => count($historial) > 0 ? $historial : null,
                ];
            }
        }
        return $licComercial;
    }

    /** Obtenemos los datos de Carnet de manipulacion */
    private static function getLibretasanitariaData($id_usuario)
    {
        $libretaSanitaria = self::datosLibretaSanitaria($id_usuario);

        if ($libretaSanitaria && !$libretaSanitaria instanceof ErrorException) {
            $libretaSanitaria = self::formatLibretaSanitaria($libretaSanitaria);
        }

        $libretaSanitaria = self::formatDataWithError($libretaSanitaria, 'Problame al obtener carnet de manipulacion');
        return $libretaSanitaria;
    }
}
