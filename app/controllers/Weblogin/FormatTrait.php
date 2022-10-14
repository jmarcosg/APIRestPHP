<?php

namespace App\Controllers\Weblogin;

trait FormatTrait
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
}
