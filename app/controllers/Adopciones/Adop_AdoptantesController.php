<?php

namespace App\Controllers\Adopciones;

use App\Connections\BaseDatos;
use App\Models\Adopciones\Adop_Adoptante;

class Adop_AdoptantesController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'ADOP_adoptantes';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Adop_Adoptante();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Adop_Adoptante();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Adop_Adoptante();
        $data->set($res);
        return $data->save();
    }

    public static function update($req, $id)
    {
        $data = new Adop_Adoptante();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Adop_Adoptante();
        return $data->delete($id);
    }
}
