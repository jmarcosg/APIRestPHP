<?php

namespace App\Controllers\MapaCircuitos;

use App\Connections\BaseDatos;
use App\Models\MapaCircuitos\Mapcirc_Persona;

class Mapcirc_PersonaController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'MAPCIRC_persona';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Mapcirc_Persona();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Mapcirc_Persona();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Mapcirc_Persona();
        $data->set($res);
        return $data->save();
    }

    public static function update($req, $id)
    {
        $data = new Mapcirc_Persona();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Mapcirc_Persona();
        return $data->delete($id);
    }
}
