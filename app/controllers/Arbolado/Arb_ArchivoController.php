<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Archivo;

class Arb_ArchivoController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_archivo';
    }
    public function index($param = [], $ops = [])
    {
        $data = new Arb_Archivo();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Archivo();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Arb_Archivo();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Archivo();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Archivo();
        return $data->delete($id);
    }
}
