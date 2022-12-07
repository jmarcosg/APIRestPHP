<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Models\CredencialesEmpleados\CREDEMP_Usuario;

class CREDEMP_UsuarioController
{
    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Usuario();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new CREDEMP_Usuario();
        $data->set($res);
        return $data->save();
    }
}
