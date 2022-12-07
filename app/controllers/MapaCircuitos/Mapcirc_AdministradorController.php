<?php

namespace App\Controllers\MapaCircuitos;

use App\Connections\BaseDatos;
use App\Models\MapaCircuitos\Mapcirc_Administrador;

class Mapcirc_AdministradorController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'MAPCIRC_administrador';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Mapcirc_Administrador();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Mapcirc_Administrador();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Mapcirc_Administrador();
        $data->set($res);
        return $data->save();
    }

    public static function update($req, $id)
    {
        $data = new Mapcirc_Administrador();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Mapcirc_Administrador();
        return $data->delete($id);
    }
}
