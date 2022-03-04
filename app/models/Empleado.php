<?php

namespace App\Models;

use App\Connections\BaseDatos;

class Empleado
{
    public function getByDocumentoAndGender($doc, $gender)
    {
        $sql =
            "SELECT 
                lega as numero, 
                cate as categoria 
            FROM PERSONAL.su.dbo.mae 
            WHERE doc = '0$doc' AND sexo = '$gender'";

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
