<?php

namespace App\Traits\Arbolado;


trait SolicitudPodaSql
{
    private static function getSql($where)
    {
        $sql =
            "SELECT 
                sol.id as id,
                /* Persona que inicio el tramite */
                per.Nombre as per_nombre,
                per.Documento as per_documento,
                per.DomicilioLegal as per_domicilio,
                per.Celular as per_celular,
                per.CorreoElectronico as per_email,
                per.Genero as per_genero,               

                /* Datos del inspector */
                ins.dni as ins_documento,
                ins.legajo as ins_legajo,
                ins.nombre as ins_nombre,
                
                /* Datos de la solicitud */
                sol.tipo as tipo,
                sol.solicita as solicita,
                sol.ubicacion as ubicacion,
                sol.motivo as motivo,
                sol.cantidad as cantidad,
                sol.estado as estado,
                sol.observacion as observacion,
                sol.estado as estado,
                sol.observacion as observacion,
                sol.cantidad_autorizado as cantidad_autorizado,
                sol.cantidad_reponer as cantidad_reponer,
                sol.dias_reponer as dias_reponer,
                sol.especie as especie,
                sol.constancia_danio as constancia_danio,
                sol.observacion_inspector as observacion_inspector,
                sol.fecha_alta as fecha_alta
            FROM dbo.arb_solicitudes sol
                LEFT JOIN dbo.wapPersonas per ON sol.id_wappersonas = per.ReferenciaID
                LEFT JOIN dbo.arb_inspectores ins ON sol.id_inspector = ins.id      
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

            /* Obtenemos los elementos que contienen ins en la key */
            $inspector = self::filterByIncludeKey($solicitud, 'ins_');

            /* Limpiamos los keys */
            foreach ($solicitud as $key => $elem) {
                if (str_contains($key, 'ins_')) {
                    $stringKey = explode('_', $key)[1];
                    $inspector[$stringKey] = $elem;
                    unset($inspector[$key]);
                    unset($solicitudes[$keySol][$key]);
                }
            }
            $solicitudes[$keySol]['inspector'] = $inspector;
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

        /* Obtenemos los elementos que contienen per_ini_ en la key */
        $inspector = self::filterByIncludeKey($solicitud, 'ins_');

        /* Limpiamos los keys */
        foreach ($solicitud as $key => $elem) {
            if (str_contains($key, 'ins_')) {
                $stringKey = explode('_', $key)[1];
                $inspector[$stringKey] = $elem;
                unset($solicitud[$key]);
                unset($inspector[$key]);
            }
        }
        $solicitud['inspector'] = $inspector;

        return $solicitud;
    }

    private static function getSqlPodadores($where)
    {
        $sql =
            "SELECT 
            sol.id as id,
            /* Persona que inicio el tramite */
            per.Nombre as per_nombre,
            per.Documento as per_documento,
            per.DomicilioLegal as per_domicilio,
            per.Celular as per_celular,
            per.CorreoElectronico as per_email,
            per.Genero as per_genero,      
            
            /* Datos de la solicitud */
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
        WHERE $where AND sol.deleted_at IS NULL
        ORDER BY id DESC";

        return $sql;
    }

    private static function filterByIncludeKey($array, $key)
    {
        return array_filter($array, function ($elem) use ($key) {
            return str_contains($elem, $key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
