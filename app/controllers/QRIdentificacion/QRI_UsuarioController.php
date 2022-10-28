<?php

namespace App\Controllers\QRIdentificacion;

use App\Connections\BaseDatos;
use App\Models\QRIdentificacion\QRI_Usuario;

class QRI_UsuarioController {
    public function __construct()
    {
        $GLOBALS['exect'][] = 'QRI_usuario';
    }

    public static function index($param = [], $ops = []) {
        $data = new QRI_Usuario();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res) {
        $data = new QRI_Usuario();
        $data->set($res);
        return $data->save();
    }
}