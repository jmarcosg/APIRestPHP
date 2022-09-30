<?php

namespace App\Controllers\Adopciones;

use App\Connections\BaseDatos;
use App\Models\Adopciones\Adop_Animal;

class Adop_AnimalesController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'ADOP_Animales';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Adop_Animal();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Adop_Animal();
        $data->set($res);
        return $data->save();
    }
}