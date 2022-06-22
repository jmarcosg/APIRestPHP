<?php

namespace App\Controllers\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\LicenciaComercial\Lc_Rubro;

class Lc_RubroController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_rubro';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Lc_Rubro();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Lc_Rubro();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Lc_Rubro();
        $data->set($res);
        return $data->save();
    }

    public function updateFirts($req, $id)
    {
        $data = new Lc_Rubro();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Lc_Rubro();
        return $data->delete($id);
    }

    public function deleteBySolicitudId($id)
    {
        $conn = new BaseDatos();
        $result = $conn->delete('lc_rubros', ['id_solicitud' => $id]);
    }
}