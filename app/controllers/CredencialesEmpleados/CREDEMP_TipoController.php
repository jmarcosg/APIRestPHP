<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Models\CredencialesEmpleados\CREDEMP_Tipo;

class CREDEMP_TipoController
{
    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Tipo();
        $data = $data->list($param, $ops)->value;
        return $data;
    }
}
