<?php

namespace App\Controllers\Weblogin;

use App\Models\BaseModel;
use ErrorException;

trait SqlQuery
{
    private function sqlViewFetch($referenciaId, $doc)
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

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }

    public function viewFetch($referenciaId, $dni)
    {
        /** Determina que vamos a llamar desde el front */
        $data = $this->sqlViewFetch($referenciaId, $dni);

        $data['legajo'] = $data['legajo'] != null ? true : false;
        $data['libreta'] = $data['libreta'] != null ? true : false;
        $data['licencia'] = $data['licencia'] == null || $data['licencia'] == -1 ? false : true;
        $data['acarreo'] = $data['acarreo'] != null ? true : false;

        return $data;
    }
}
