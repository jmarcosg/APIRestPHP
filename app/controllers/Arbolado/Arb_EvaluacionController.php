<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Audit;
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
        $id = $data->save();

        /* Generamos registro para la auditoria */
        $audit = new Arb_Audit();
        $audit->set([
            'id_usuario' => $res['id_usuario_admin'],
            'id_wappersonas' => $res['id_wappersonas_admin'],
            'id_evaluacion' => $id,
            'accion' => 'agrego',
        ]);
        $audit->save();

        return $id;
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

    public function getEstadoEvaluacion()
    {
    }
}
