<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Models\CredencialesEmpleados\CREDEMP_Valor;

class CREDEMP_ValorController
{
    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Valor();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new CREDEMP_Valor();

        $data->set($res);
        return $data->save();
    }

    public static function update($data, $id)
    {
        $valor = new CREDEMP_Valor();
        return $valor->update($data, $id);
    }
}
