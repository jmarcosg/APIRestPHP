<?php

namespace App\Models;

use ErrorException;

class Empleado extends BaseModel
{
    protected $logPath = 'v1/empleado';

    public function getByDocumentoAndGender($doc, $gender)
    {
        $sql =
            "SELECT 
                lega as numero, 
                cate as categoria 
            FROM PERSONAL.su.dbo.mae 
            WHERE doc = '0$doc' AND sexo = '$gender'";

        $result = $this->executeSqlQuery($sql);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }
}
