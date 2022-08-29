<?php

namespace App\Traits\LicenciaComercial;

trait QuerysSql
{
    private static function getSqlSolicitudes($where)
    {
        $sql =
            "SELECT 
                sol.id as id,
                /* Persona que inicio el tramite */
                perini.Nombre as perini_nombre,
                perini.Documento as perini_documento,
                perini.cuil as perini_cuil,
                perini.DomicilioLegal as perini_domicilio,
                perini.Celular as perini_celular,
                perini.CorreoElectronico as perini_email,
                perini.Genero as perini_genero,
                
                /* Persona que recibe el tramite */
                sol.id_wappersonas_tercero as id_wappersonas_tercero,
                persol.Nombre as persol_nombre,
                persol.Documento as persol_documento,
                persol.cuil as persol_cuil,
                persol.DomicilioLegal as persol_domicilio,
                persol.Celular as persol_celular,
                persol.CorreoElectronico as persol_email,
                persol.Genero as persol_genero,
                
                /* Datos de la solicitud */
                sol.nro_expediente as nro_expediente,
                sol.nro_licencia as nro_licencia,
                sol.telefono as telefono,
                sol.correo as correo,
                sol.domicilio_particular as domicilio_particular,
                sol.pertenece as pertenece,
                sol.dni_tercero as dni_tercero,
                sol.tramite_tercero as tramite_tercero,
                sol.genero_tercero as genero_tercero,
                sol.tipo_persona as tipo_persona,
                sol.cuit as cuit,
                sol.tiene_local as tiene_local,
                sol.nomenclatura as nomenclatura,
                sol.m2 as m2,
                sol.nombre_fantasia as nombre_fantasia,
                sol.direccion_comercial as direccion_comercial,
                sol.descripcion_actividad as descripcion,
                sol.estado as estado,
                sol.observacion as observacion,
                sol.ver_inicio as ver_inicio,
                sol.ver_rubros as ver_rubros,
                sol.ver_catastro as ver_catastro,
                sol.ver_ambiental as ver_ambiental,
                sol.ver_documentos as ver_documentos,
                sol.notas_catastro as notas_catastro,
                sol.notas_ambiente as notas_ambiente,
                sol.fecha_alta as fecha_alta
            FROM dbo.lc_solicitudes sol
                LEFT JOIN dbo.wapPersonas perini ON sol.id_wappersonas = perini.ReferenciaID 
                LEFT JOIN dbo.wapPersonas persol ON sol.id_wappersonas_tercero = persol.ReferenciaID
            WHERE $where
            ORDER BY id DESC";

        return $sql;
    }

    private static function getSqlHistorial($where)
    {
        $sql =
            "SELECT                 
                /* Persona que evaluo el tramite */
                peradm.Nombre as admin,
                
                /* Datos de la solicitud */
                sol.id as id,
                sol.id_solicitud as id_solicitud,
                sol.estado as estado,
                sol.observacion as observacion,
                sol.tipo_registro as tipo_registro,
                sol.visto as visto,
                sol.fecha_alta as fecha_alta
            FROM dbo.lc_solicitudes_historial sol
                LEFT JOIN dbo.wapPersonas peradm ON sol.id_wappersonas_admin = peradm.ReferenciaID 
            WHERE $where
            ORDER BY id DESC";

        return $sql;
    }

    private static function formatSolicitudDataArray($solicitudes)
    {
        foreach ($solicitudes as $keySol => $solicitud) {

            /* Obtenemos los elementos que contienen per_ini_ en la key */
            $personaInicio = self::filterByIncludeKey($solicitud, 'perini_');


            /* Limpiamos los keys */
            foreach ($solicitud as $key => $elem) {
                if (str_contains($key, 'perini_')) {
                    $stringKey = explode('_', $key)[1];
                    $personaInicio[$stringKey] = $elem;
                    unset($personaInicio[$key]);
                    unset($solicitudes[$keySol][$key]);
                }
            }
            $solicitudes[$keySol]['personaInicio'] = $personaInicio;


            if ($solicitud["id_wappersonas_tercero"]) {
                /* Obtenemos los elementos que contienen per_sol_ en la key */
                $personaTercero = self::filterByIncludeKey($solicitud, 'persol_');

                /* Limpiamos los keys */
                foreach ($solicitud as $key => $elem) {
                    if (str_contains($key, 'persol_')) {
                        $stringKey = explode('_', $key)[1];
                        $personaTercero[$stringKey] = $elem;
                        unset($personaTercero[$key]);
                        unset($solicitudes[$keySol][$key]);
                    }
                }
                $solicitudes[$keySol]['personaTercero'] = $personaTercero;
            } else {
                $solicitudes[$keySol]['personaTercero'] = null;
                /* Limpiamos los keys */
                foreach ($solicitud as $key => $elem) {
                    if (str_contains($key, 'persol_')) {
                        unset($solicitudes[$keySol][$key]);
                    }
                }
            }
        }
        return $solicitudes;
    }

    private static function formatSolicitudData($solicitud)
    {

        /* Obtenemos los elementos que contienen per_ini_ en la key */
        $personaInicio = self::filterByIncludeKey($solicitud, 'perini_');


        /* Limpiamos los keys */
        foreach ($solicitud as $key => $elem) {
            if (str_contains($key, 'perini_')) {
                $stringKey = explode('_', $key)[1];
                $personaInicio[$stringKey] = $elem;
                unset($solicitud[$key]);
                unset($personaInicio[$key]);
            }
        }
        $solicitud['personaInicio'] = $personaInicio;


        if ($solicitud["id_wappersonas_tercero"]) {
            /* Obtenemos los elementos que contienen per_sol_ en la key */
            $personaTercero = self::filterByIncludeKey($solicitud, 'persol_');

            /* Limpiamos los keys */
            foreach ($solicitud as $key => $elem) {
                if (str_contains($key, 'persol_')) {
                    $stringKey = explode('_', $key)[1];
                    $personaTercero[$stringKey] = $elem;
                    unset($personaTercero[$key]);
                    unset($solicitud[$key]);
                }
            }
            $solicitud['personaTercero'] = $personaTercero;
        } else {
            $solicitud['personaTercero'] = null;
            /* Limpiamos los keys */
            foreach ($solicitud as $key => $elem) {
                if (str_contains($key, 'persol_')) {
                    unset($solicitud[$key]);
                }
            }
        }
        return $solicitud;
    }

    private static function filterByIncludeKey($array, $key)
    {
        return array_filter($array, function ($elem) use ($key) {
            return str_contains($elem, $key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
