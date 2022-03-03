<?php

namespace App\Models;

use ErrorException;

use App\Connections\BaseDatos;

class LicenciaConducir
{
    public function getByDocumento($id)
    {
        $sql =
            "SELECT 
                Clase as clase,
                FechaVigencia as venc,
                Domicilio as direccion,
                Insumo as insumo
            FROM dbo.licLicencias 
                WHERE Licencia = $id";

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
