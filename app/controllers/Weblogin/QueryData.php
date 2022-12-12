<?php

namespace App\Controllers\Weblogin;

use App\Controllers\Common\ImponiblesController;
use App\Models\Weblogin\Weblogin;
use ErrorException;

trait QueryData
{
    /**
     * Se determina en varios factores que datos vamos a traer para cuando se concrete el login
     * @param mixed $referenciaId
     * @param mixed $dni
     * @return \Throwable|array|bool
     */
    public static function viewFetch($referenciaId, $dni)
    {

        $data = self::sqlViewFetch($referenciaId, $dni);

        $data['acarreo'] = false;
        if (FETCH_ACARREO) {
            $rodados = ImponiblesController::getRodados(22089786);
            if ($rodados) {
                $acarreos = self::getAcarreos($rodados);
                if (!$acarreos instanceof ErrorException && count($acarreos) > 0) {
                    $data['acarreo'] = $acarreos;
                }
            }
        }

        $data['licencia_comercial'] = (FETCH_LEGAJO && $data['licencia_comercial'] != null && $data['licencia_comercial'] != "0") ? true : false;
        $data['muniEventos'] = FETCH_LEGAJO && self::getMuniEventosFetch($dni) ? true : false;
        $data['legajo'] = $data['legajo'] != null && FETCH_LEGAJO ? true : false;
        $data['libreta'] = $data['libreta'] != null && FETCH_LIBRETA ? true : false;
        $data['libretaDos'] = FETCH_LIBRETA ? true : false;
        $data['licencia'] = ($data['licencia'] == null || $data['licencia'] == -1) && FETCH_LICENCIA ? false : true;

        return $data;
    }

    /**
     * Consulta SQL para los posibles datos que se pueden llegar a solicitar cuando concrete el login del usuario
     * legajo | libreta | licencia_comercial | licenca_conducir
     * @param mixed $referenciaId
     * @param mixed $doc
     * @return \Throwable|array|bool
     */
    private static function sqlViewFetch($referenciaId, $doc)
    {
        $sql =
            "SELECT 
            (SELECT AppID FROM  wapUsuariosPerfiles WHERE ReferenciaID = $referenciaId AND AppID = 19) as legajo,
            (
                SELECT
                TOP 1
                    sol.id as id
                FROM wapUsuarios wu
                    LEFT JOIN wapPersonas per ON per.ReferenciaID = wu.PersonaID
                    LEFT JOIN libretas_usuarios usu ON usu.id_wappersonas = per.ReferenciaID
                    LEFT JOIN libretas_solicitudes sol ON sol.id_usuario_solicitante = usu.id
                WHERE wu.ReferenciaID = $referenciaId ORDER BY id DESC
            ) AS libreta,
            (SELECT COUNT(id) FROM lc_solicitudes WHERE id_usuario = $referenciaId) as licencia_comercial,
            /* (SELECT COUNT(id) FROM lc_solicitudes WHERE id_usuario = $referenciaId AND visto = 0) as licencia_comercial, */
            (SELECT insumo FROM licLicencias WHERE Licencia = $doc) as licencia";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    /**
     * Summary of getAcarreos
     * @param array $rodados
     * @return \Throwable|array|bool
     */
    private static function getAcarreos(array $rodados)
    {
        $where = '(';
        foreach ($rodados as $rodado) {
            $where .= "patente = '$rodado->identificacion' OR ";
        }
        $where = rtrim($where, " OR ");

        $where .= ") AND a.ID_PERSONA IS NULL";
        $sql =
            "SELECT
            a.ID_ACARREO as id_acarreo,
            a.PATENTE AS patente,
            a.NUM_RECIBO_PAGO as recibo,
            m.ID_MOTIVO as id_motivo,
            m.NOMBRE AS motivo,
            p.NOMBRE AS playa,
            p.DESCRIPCION AS direccion,
            a.FECHA_HORA as fecha
            FROM dbo.AC_ACARREO a
            LEFT JOIN AC_MOTIVO m ON m.ID_MOTIVO = a.ID_MOTIVO
            LEFT JOIN AC_PLAYA p ON p.ID_PLAYA= a.ID_PLAYA
            WHERE $where";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql, false);
        return $result;
    }

    /**
     * Summary of datosLegajo
     * @param mixed $gender
     * @param mixed $doc
     * @return \Throwable|array|bool
     */
    public static function datosLegajo($gender, $doc)
    {
        $sql =
            "SELECT 
                lega as numero, 
                cate as categoria 
            FROM PERSONAL.su.dbo.mae 
            WHERE doc = '0$doc' AND sexo = '$gender'";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    /**
     * Summary of datosAccareo
     * @param mixed $id
     * @return \Throwable|array|bool
     */
    public static function datosAccareo($id)
    {
        $sql =
            "SELECT
                a.ID_ACARREO as id_acarreo,
                a.PATENTE AS patente,
                m.ID_MOTIVO as id_motivo,
                m.NOMBRE AS motivo,
                p.NOMBRE AS playa,
                p.DESCRIPCION AS direccion,
                a.FECHA_HORA as fecha
            FROM dbo.wapUsuarios wu
                LEFT JOIN AC_ACARREO a ON a.ID_PERSONA = wu.PersonaID 
                LEFT JOIN AC_MOTIVO m ON m.ID_MOTIVO = a.ID_MOTIVO
                LEFT JOIN AC_PLAYA p ON p.ID_PLAYA= a.ID_PLAYA
            WHERE wu.ReferenciaID = $id and a.BORRADO_LOGICO = 'NO'";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    /**
     * Summary of datosLicConducir
     * @param mixed $id
     * @return string
     */
    public static function datosLicConducir($id)
    {
        $sql =
            "SELECT 
                SubClaseID as subclase,
                Categoria as categoria,
                FechaVigencia as venc,
                FechaEmision as emision,
                Domicilio as direccion,
                GrupoSangre as grupo_sangre,
                Donante as donante,
                Insumo as insumo
            FROM dbo.licLicencias 
            WHERE Licencia = $id";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    /**
     * Summary of datosHistorialLicComercial
     * @param mixed $id
     * @return string
     */
    public static function datosHistorialLicComercial($id)
    {
        $sql = "SELECT * FROM lc_solicitudes_historial WHERE id_usuario = $id AND visto = 0";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    public static function datosLicComercial($id)
    {
        $sql =
            "SELECT 
                lc_sol.id as id,
                lc_sol.estado as estado,
                (SELECT COUNT(id) FROM lc_solicitudes_historial WHERE id_solicitud = lc_sol.id) as historial
        
            FROM lc_solicitudes lc_sol 
            WHERE id_usuario = $id AND estado NOT LIKE '%rechazado%'";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql, false);
        return $result;
    }

    public static function datosLibretaSanitaria($id)
    {
        $sql =
            "SELECT TOP 1
                sol.id as id,
                sol.estado as estado,
                sol.nro_recibo as recibo,
                sol.fecha_vencimiento as venc,
                sol.fecha_alta as fecha_alta,
                sol.fecha_evaluacion as fecha_evaluacion
            FROM wapUsuarios wu
                LEFT JOIN wapPersonas per ON per.ReferenciaID = wu.PersonaID
                LEFT JOIN libretas_usuarios usu ON usu.id_wappersonas = per.ReferenciaID
                LEFT JOIN libretas_solicitudes sol ON sol.id_usuario_solicitante = usu.id
            WHERE wu.ReferenciaID = $id ORDER BY id DESC";

        $model = new Weblogin();
        $result = $model->executeSqlQuery($sql);

        if (!$result instanceof ErrorException) {
            $result['wqr'] = 'asdads';
        }
        return $result;
    }

    public static function getMuniEventosFetch($dni)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_MUNI_EVENTOS . 38493877,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        return $response['data'];
    }

    /* wlFotosPerfil */

    /**
     * Summary of getPersonsSql
     * @param mixed $where
     * @return string
     */
    public static function getPersonsSql($where)
    {
        $sql =
            "SELECT
            fUsr.id as id,
            fUsr.foto_perfil as foto_perfil,
            fUsr.foto_dni as foto_dni,    
            fUsr.id_app as id_app,            
            CASE
                WHEN fUsr.id_usuario IS NOT NULL      
                THEN wapPerUsr.Nombre       
                ELSE wapPer.Nombre       
            END as nombre,
            
            CASE
                WHEN fUsr.id_usuario IS NOT NULL      
                THEN wapPerUsr.Documento       
                ELSE wapPer.Documento       
            END as dni,   
            CASE

                WHEN fUsr.id_usuario IS NOT NULL      
                THEN wapPerUsr.Genero       
                ELSE wapPer.Genero       
            END as genero,   
            
            CASE
                WHEN fUsr.id_usuario IS NOT NULL      
                THEN wapPerUsr.DomicilioLegal       
                ELSE wapPer.DomicilioLegal       
            END as dom_legal, 
            
            CASE
                WHEN fUsr.id_usuario IS NOT NULL      
                THEN wapPerUsr.DomicilioReal       
                ELSE wapPer.DomicilioReal       
            END as dom_real,
            
            fUsr.estado as estado,
            apps.APLICACION as aplicacion,
            fUsr.observacion as observacion    
            
            FROM dbo.wlFotosUsuarios fUsr    
                LEFT JOIN dbo.wapUsuarios wapUsr ON fUsr.id_usuario = wapUsr.ReferenciaID    
                LEFT JOIN dbo.wapPersonas wapPer ON fUsr.id_persona = wapPer.ReferenciaID    
                LEFT JOIN dbo.wapPersonas wapPerUsr ON wapUsr.PersonaID = wapPerUsr.ReferenciaID 
                LEFT JOIN dbo.wlAplicaciones apps ON fUsr.id_app = apps.REFERENCIA 
            WHERE $where
            ORDER BY fUsr.fecha_alta DESC";

        return $sql;
    }
}
