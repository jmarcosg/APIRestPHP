<?php

namespace App\Controllers;


use App\Models\WapUsuariosPerfiles;

class WapUsuariosPerfilesController
{
    public function __construct()
    {
        $_SESSION['exect'][] = 'wapUsuariosPerfiles';
    }

    /* Buscar todos los usuarios */
    public function index($param = [], $ops = [])
    {
        $wapUsuPerfiles = new WapUsuariosPerfiles();
        $wapUsuPerfiles = $wapUsuPerfiles->list($param, $ops);
        return $wapUsuPerfiles->value;
    }

    /* Busca un usuario */
    public function get($params)
    {
        $wapUsuPerfiles = new WapUsuariosPerfiles();
        $wapUsuPerfiles = $wapUsuPerfiles->get($params);
        return $wapUsuPerfiles->value;
    }

    /* Guarda un formulario */
    public function store($res)
    {
        $wapUsuPerfiles = new WapUsuariosPerfiles();
        $wapUsuPerfiles->set($res);
        return $wapUsuPerfiles->save();
    }

    /* Actualiza un form */
    public function update($req, $id)
    {
        $wapUsuPerfiles = new WapUsuariosPerfiles();
        return $wapUsuPerfiles->update($req, $id);
    }

    /* Actualiza un form */
    public function delete($id)
    {
        $wapUsuPerfiles = new WapUsuariosPerfiles();
        return $wapUsuPerfiles->delete($id);
    }
}
