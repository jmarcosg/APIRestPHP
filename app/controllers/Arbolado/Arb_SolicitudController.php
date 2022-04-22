<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Solicitud;

class Arb_SolicitudController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_solicitud';
    }
    public function index($param = [], $ops = [])
    {
        $data = new Arb_Solicitud();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Solicitud();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Arb_Solicitud();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Solicitud();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Solicitud();
        return $data->delete($id);
    }
}
