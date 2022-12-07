<?php

namespace App\Controllers\MapaCircuitos;

use App\Connections\BaseDatos;
use App\Models\MapaCircuitos\Mapcirc_Circuito;

class Mapcirc_CircuitoController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'MAPCIRC_circuito';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Mapcirc_Circuito();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Mapcirc_Circuito();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Mapcirc_Circuito();
        $data->set($res);
        return $data->save();
    }

    public static function update($req, $id)
    {
        $data = new Mapcirc_Circuito();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Mapcirc_Circuito();
        return $data->delete($id);
    }
}
