<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Evaluacion;

class Arb_EvaluacionController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_evaluacion';
    }
    public function index($param = [], $ops = [])
    {
        $data = new Arb_Evaluacion();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Evaluacion();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Arb_Evaluacion();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Evaluacion();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Evaluacion();
        return $data->delete($id);
    }
}
