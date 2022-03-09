<?php

namespace App\Models;

use ErrorException;

class TotemsData extends BaseModel
{
    protected $logPath = 'v1/totems-data';

    public function groupByMonth($year, $totem)
    {
        $sql =
            "SELECT 
                MONTH (timeStamp) AS value, 
                COUNT(*) AS dominios
            FROM  totemsDataOK
                WHERE (totemID = $totem) AND (timeStamp BETWEEN '01/01/$year' AND '31/12/$year')
                GROUP BY MONTH(timeStamp)
                ORDER BY value";

        $result = $this->executeSqlQuery($sql, false);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }

    public function groupByDay($year, $month, $totem)
    {
        $days = cal_days_in_month(CAL_GREGORIAN, intval($month), intval($year));
        $sql =
            "SELECT 
                DAY (timeStamp) AS value, 
                COUNT(*) AS dominios
            FROM  totemsDataOK
                WHERE (totemID = $totem) AND (timeStamp BETWEEN '01/$month/$year' AND '$days/$month/$year 23:59:59')
                GROUP BY DAY(timeStamp)
                ORDER BY value";

        $result = $this->executeSqlQuery($sql, false);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }

    public function completeArrayMonths($unArraysito, $meses, $inicio)
    {
        $puntero = 0;
        $contador = 1;
        $salida = [];

        while ($contador - 1 < $meses) {
            if ($unArraysito[$puntero]['value'] == "$contador") {
                $salida[$contador - 1] = $unArraysito[$puntero];
                $puntero++;
            } else {
                $salida[$contador - 1] = [
                    "value" => "$contador",
                    'dominios' => $this->generateNumber($unArraysito, $contador - 1)
                ];
                $unArraysito[] = $salida[$contador - 1];
            }
            $contador++;
        }
        return $salida;
    }

    public function generateNumber($result, $index)
    {
        $value = intval($result[$index]['dominios']) * 1.2 - intval($result[$index]['dominios']) / 2;
        $value = intval($value);
        return "$value";
    }
}
