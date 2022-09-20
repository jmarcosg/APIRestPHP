<?php

namespace App\Controllers\Weblogin;

use App\Models\BaseModel;
use ErrorException;

trait SqlQuery
{
    public static function viewFetch($referenciaId, $dni)
    {
        /** Determina que vamos a llamar desde el front */
        $data = self::sqlViewFetch($referenciaId, $dni);

        $data['legajo'] = $data['legajo'] != null ? true : false;
        $data['libreta'] = $data['libreta'] != null ? true : false;
        $data['licencia'] = $data['licencia'] == null || $data['licencia'] == -1 ? false : true;
        $data['acarreo'] = $data['acarreo'] != null ? true : false;

        return $data;
    }

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
            (SELECT insumo FROM licLicencias WHERE Licencia = $doc) as licencia,
            (
            SELECT 
                a.PATENTE as patente
            FROM dbo.wapUsuarios wu
                LEFT JOIN AC_ACARREO a ON a.ID_PERSONA = wu.PersonaID
            WHERE wu.ReferenciaID = $referenciaId and a.BORRADO_LOGICO = 'NO'
            ) as acarreo";

        $model = new BaseModel();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    public static function datosLegajo($gender, $doc)
    {
        $sql =
            "SELECT 
                lega as numero, 
                cate as categoria 
            FROM PERSONAL.su.dbo.mae 
            WHERE doc = '0$doc' AND sexo = '$gender'";

        return $sql;
    }

    public static function datosAccareo($id)
    {
        $sql =
            "SELECT
                a.PATENTE AS patente,
                m.NOMBRE AS motivo,
                p.NOMBRE AS playa,
                p.DESCRIPCION AS direccion,
                a.FECHA_HORA as fecha
            FROM dbo.wapUsuarios wu
                LEFT JOIN AC_ACARREO a ON a.ID_PERSONA = wu.PersonaID 
                LEFT JOIN AC_MOTIVO m ON m.ID_MOTIVO = a.ID_MOTIVO
                LEFT JOIN AC_PLAYA p ON p.ID_PLAYA= a.ID_PLAYA
            WHERE wu.ReferenciaID = $id and a.BORRADO_LOGICO = 'NO'";

        return $sql;
    }
}
