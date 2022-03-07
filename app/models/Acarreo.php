<?php

namespace App\Models;

use ErrorException;

class Acarreo extends BaseModel
{
    protected $logPath = 'v1/acarreo';

    public function getByReferenciaId($id)
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

        $result = $this->executeSqlQuery($sql);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }
}
