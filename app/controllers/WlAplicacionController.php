<?php

namespace App\Controllers;


use App\Models\WlAplicacion;

class WlAplicacionController
{
    public function __construct()
    {
        $_SESSION['exect'][] = 'wlAplicacion';
    }

    /* Buscar todos los usuarios */
    public function index($param = [], $ops = [])
    {
        $wlApp = new WlAplicacion();
        $wlApp = $wlApp->list($param, $ops);
        return $wlApp->value;
    }

    /* Busca un usuario */
    public function get($params)
    {
        $wlApp = new WlAplicacion();
        $wlApp = $wlApp->get($params);
        return $wlApp->value;
    }

    /* Guarda un formulario */
    public function store($res)
    {
        $wlApp = new WlAplicacion();
        $wlApp->set($res);
        return $wlApp->save();
    }

    /* Actualiza un form */
    public function update($req, $id)
    {
        $wlApp = new WlAplicacion();
        return $wlApp->update($req, $id);
    }

    /* Actualiza un form */
    public function delete($id)
    {
        $wlApp = new WlAplicacion();
        return $wlApp->delete($id);
    }
}
