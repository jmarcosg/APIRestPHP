<?php

namespace App\Controllers\Adopciones;

use App\Connections\BaseDatos;
use App\Models\Adopciones\Adop_Vecino;

class Adop_VecinosController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'ADOP_Vecinos';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Adop_Vecino();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Adop_Vecino();
        $data->set($res);
        return $data->save();
    }
}
