<?php

namespace App\Controllers\QRIdentificacion;

use App\Connections\BaseDatos;
use App\Models\QRIdentificacion\QRI_Persona;

class QRI_PersonaController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'QRI_persona';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new QRI_Persona();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new QRI_Persona();
        $data->set($res);
        return $data->save();
    }
}
