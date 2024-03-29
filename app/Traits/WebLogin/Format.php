<?php

namespace App\Traits\WebLogin;

use DateTime;
use ErrorException;

trait Format
{
    private static function formatLicConducir(array $result)
    {
        /* Formateo del estado - Vigente, Vencida, Por vencer */
        $arrayFechas = compararFechas($result['venc'], 'days', 'Y-m-d');

        $diferenciaDias = $arrayFechas['dif'];

        $result['estado'] = 'Vigente';
        $result['vencida'] = false;

        /* Detectamos si hay una diferencia, este valor puede ser temporal, en el caso qeu este vencida */
        if ($diferenciaDias <= 60) {
            $result['estado'] = 'Por vencer';
        }

        /* Detectamos si ya se encuenta vencida */
        if ($arrayFechas['date'] <= $arrayFechas['now']) {
            $result['estado'] = 'Vencida';
            $result['vencida'] = true;
        };

        /* Diferencia de dias */
        $result['dias_venc'] = $diferenciaDias;

        /* Detectamos si es licencia profesional */
        $result['profesional'] = false;
        $result['show_curso_pro'] = false;
        if (!str_contains($result['subclase'], 'A') && !str_contains($result['subclase'], 'B')) {
            $result['pro'] = true;

            if ($result['estado'] == 'Vencida' && $diferenciaDias >= 90) {
                $result['show_curso_pro'] = true;
            }
        }

        $result['show_renovacion_b'] = false;
        if (str_contains($result['subclase'], 'B') && $result['estado'] == 'Vencida') {
            $result['show_renovacion_b'] = true;
        }

        $result['show_init_tramite'] = false;
        if ($result['estado'] != 'Vigente' || $diferenciaDias <= 60) {
            $result['show_init_tramite'] = true;
        }

        /* Formateo de donante */
        $result['donante'] = true;
        if ($result['donante'] == 0) {
            $result['donante'] = false;
        }

        return $result;
    }
    private static function formatLibretaSanitaria(array $result)
    {
        $date = DateTime::createFromFormat('d/m/Y', $result['venc'])->format('Y-m-d H:i:s');
        $arrayFechas = compararFechas($date, 'days', 'Y-m-d');

        /* Para generar el orden en el Front */
        $result['fecha_ref'] = DateTime::createFromFormat('d/m/Y', $result['fecha_evaluacion'])->format('Ymd');

        $result['show_renovar'] = false;
        if ($arrayFechas['date'] <= $arrayFechas['now']) {
            $result['estado'] = 'Vencido';
            $result['show_renovar'] = true;
        }

        if ($result['estado'] != 'Nuevo') {
            $urlCarnet = BASE_WEBLOGIN_APPS . 'libretasanitaria/public/views/carnet/index.php?solicitud=' . $result['id'];
            $result['carnet'] = $urlCarnet;
            $result['qr_carnet'] = getQrByUlr($urlCarnet, 150, 150, '3a8fda');
        }

        /* Url para ingresar a la APP */
        $result['url_init_app'] = BASE_WEBLOGIN_APPS . 'libretasanitaria/public/index.php?SESSIONKEY=';
        /* Entre el init y el end se debe ingresar la SESSIONKEY */
        $result['url_end_app'] = '&APP=53&ROLES=3';


        return $result;
    }
    private static function formatLicenciaComercial(array $result)
    {
        $data = [
            'count_not' => count($result),
            'data' => group_by("id_solicitud", $result)
        ];

        foreach ($data['data'] as $key => $d) {
            $data['data'][$key] = [
                'count' => count($d),
                'solicitud' => $key,
                'data' => $d
            ];
        }
        $data['data'] = array_values($data['data']);

        return $data;
    }

    /**
     * Genera un formato para una respuesta, si requiere enviar un error array vacio se debe ingresar @param mixed $msgErrorArray
     * @param mixed $data
     * @param mixed $msgError
     * @param mixed $msgErrorArray
     * @return array
     */
    private static function formatDataWithError(array $data, string $msgError = null, string $msgErrorArray = null): array
    {
        $error = null;
        if ($data && $data instanceof ErrorException) {
            $error = $msgError;
            $data = null;
        }

        if (is_array($data) && count($data) == 0 && $msgError) {
            $error = $msgErrorArray;
            $data = null;
        }

        return [
            'data' => $data,
            'fetch' => true,
            'loading' => false,
            'error' => $error
        ];
    }
}
