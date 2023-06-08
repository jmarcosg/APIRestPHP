<?php

namespace App\Controllers\JuegoFiestaConfluencia;

use App\Connections\BaseDatos;
use App\Models\JuegoFiestaConfluencia\MEMCONF_Configuracion;

class MEMCONF_ConfiguracionController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'MEMCONF_Configuracion';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new MEMCONF_Configuracion();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new MEMCONF_Configuracion();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new MEMCONF_Configuracion();
        $data->set($res);
        return $data->save();
    }

    public static function update($req, $id)
    {
        $data = new MEMCONF_Configuracion();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new MEMCONF_Configuracion();
        return $data->delete($id);
    }
}
