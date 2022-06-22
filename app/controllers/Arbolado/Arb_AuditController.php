<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Audit;

class Arb_AuditController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_audit';
    }
    public function index($param = [], $ops = [])
    {
        $data = new Arb_Audit();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Audit();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Arb_Audit();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Audit();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Audit();
        return $data->delete($id);
    }
}
