<?php

namespace App\Models;

use ErrorException;

class LicenciaConducir extends BaseModel
{
    protected $logPath = 'v1/libreta-sanitaria';

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

        $result = $this->executeSqlQuery($sql);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        $result = $this->changeResultFormat($result);
        return $result;
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
