<?php

namespace App\Models;

use ErrorException;

use App\Connections\BaseDatos;

class Acarreo
{
    public function getByReferenciaId($id)
    {
        $sql =
            "SELECT
                a.PATENTE AS patente,
                m.NOMBRE AS motivo,
                p.NOMBRE AS playa,
                p.DESCRIPCION AS direccion
            FROM dbo.wapUsuarios wu
                LEFT JOIN AC_ACARREO a ON a.ID_PERSONA = wu.PersonaID 
                LEFT JOIN AC_MOTIVO m ON m.ID_MOTIVO = a.ID_MOTIVO
                LEFT JOIN AC_PLAYAs p ON p.ID_PLAYA= a.ID_PLAYA
            WHERE wu.ReferenciaID = $id and a.BORRADO_LOGICO = 'NO'";

        try {
            $conn = new BaseDatos();
            $query =  $conn->query($sql);
            $result = $conn->fetch_assoc($query);
            return $result;
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
