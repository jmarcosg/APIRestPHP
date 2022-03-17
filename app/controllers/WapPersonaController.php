<?php

namespace App\Controllers;


use App\Models\WapPersona;

class WapPersonaController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'wapPersona';
    }

    /* Buscar todos los usuarios */
    public function index($param = [], $ops = [])
    {
        $wapPersona = new WapPersona();
        $wapPersona = $wapPersona->list($param, $ops);
        return $wapPersona->value;
    }

    /* Busca un usuario */
    public function get($params)
    {
        $wapPersona = new WapPersona();
        $wapPersona = $wapPersona->get($params);
        return $wapPersona->value;
    }

    /* Guarda un formulario */
    public function store($res)
    {
        $wapPersona = new WapPersona();
        $wapPersona->set($res);
        return $wapPersona->save();
    }

    /* Actualiza un form */
    public function update($req, $id)
    {
        $wapPersona = new WapPersona();
        return $wapPersona->update($req, $id);
    }

    /* Actualiza un form */
    public function delete($id)
    {
        $wapPersona = new WapPersona();
        return $wapPersona->delete($id);
    }
}
