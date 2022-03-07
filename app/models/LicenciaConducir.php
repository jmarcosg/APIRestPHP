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
            $result = $this->changeResultFormat($result);
            return $result;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function changeResultFormat(array $result)
    {
        $result['estado'] = 'Vigente';
        if ($result['venc']) {
            $result['venc'] = substr($result['venc'], 0, 10);
            $arrayFechas = compararFechas($result['venc'], 'days', 'Y-m-d');
            if ($arrayFechas['dif'] <= 7) $result['estado'] = 'Por vencer';
            if ($arrayFechas['date'] <= $arrayFechas['now']) $result['estado'] =  'Vencida';
        }

        return $result;
    }
}
