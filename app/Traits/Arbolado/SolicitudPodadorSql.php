<?php

namespace App\Traits\Arbolado;


trait SolicitudPodadorSql
{

    private static function getSql($where)
    {
        $sql =
            "SELECT 
            sol.id as id,
            /* Persona que inicio el tramite */
            per.ReferenciaID as id_wappersonas,
            per.Nombre as per_nombre,
            per.Documento as per_documento,
            per.DomicilioLegal as per_domicilio,
            per.Celular as per_celular,
            per.CorreoElectronico as per_email,
            per.Genero as per_genero,

            /* Calificador */
            
            /* Datos de la solicitud */
            eva.fecha_evaluacion as fecha_evaluacion,    
            sol.certificado as certificado,
            sol.capacitador as capacitador,
            sol.telefono as telefono,
            sol.observacion as observacion,
            sol.estado as estado,
            sol.fecha_vencimiento as fecha_vencimiento,
            sol.fecha_revision as fecha_revision,
            sol.fecha_deshabilitado as fecha_deshabilitado,
            sol.motivo_deshabilitado as motivo_deshabilitado,
            sol.fecha_alta as fecha_alta
        FROM dbo.arb_podadores sol
            LEFT JOIN dbo.wapPersonas per ON sol.id_wappersonas = per.ReferenciaID   
            LEFT JOIN dbo.arb_evaluaciones eva ON sol.id = eva.id_podador   
        WHERE $where AND sol.deleted_at IS NULL
        ORDER BY id DESC";

        return $sql;
    }

    private static function formatDataArray($solicitudes)
    {
        foreach ($solicitudes as $keySol => $solicitud) {

            /* Obtenemos los elementos que contienen per en la key */
            $persona = self::filterByIncludeKey($solicitud, 'per_');

            /* Limpiamos los keys */
            foreach ($solicitud as $key => $elem) {
                if (str_contains($key, 'per_')) {
                    $stringKey = explode('_', $key)[1];
                    $persona[$stringKey] = $elem;
                    unset($persona[$key]);
                    unset($solicitudes[$keySol][$key]);
                }
            }

            $solicitudes[$keySol]['persona'] = $persona;
        }

        return $solicitudes;
    }

    private static function formatData($solicitud)
    {

        /* Obtenemos los elementos que contienen per_ini_ en la key */
        $persona = self::filterByIncludeKey($solicitud, 'per_');

        /* Limpiamos los keys */
        foreach ($solicitud as $key => $elem) {
            if (str_contains($key, 'per_')) {
                $stringKey = explode('_', $key)[1];
                $persona[$stringKey] = $elem;
                unset($solicitud[$key]);
                unset($persona[$key]);
            }
        }
        $solicitud['persona'] = $persona;

        return $solicitud;
    }

    private static function filterByIncludeKey($array, $key)
    {
        return array_filter($array, function ($elem) use ($key) {
            return str_contains($elem, $key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
