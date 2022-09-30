<?php

namespace App\Controllers\Adopciones;

use App\Connections\BaseDatos;
use App\Models\Adopciones\Adop_Adopcion;

class Adop_AdopcionesController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'ADOP_Adopciones';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Adop_Adopcion();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Adop_Adopcion();
        $data->set($res);
        return $data->save();
    }
}
