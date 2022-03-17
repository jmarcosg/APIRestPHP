<?php

namespace App\Controllers;


use App\Models\WapUsuario;

class WapUsuarioController
{
    public function __construct()
    {
        $_SESSION['exect'][] = 'wapUsuario';
    }

    /* Buscar todos los usuarios */
    public function index($param = [], $ops = [])
    {
        $wapUsuario = new WapUsuario();
        $wapUsuario = $wapUsuario->list($param, $ops);
        return $wapUsuario->value;
    }

    /* Busca un usuario */
    public function get($params)
    {
        $wapUsuario = new WapUsuario();
        $wapUsuario = $wapUsuario->get($params);
        return $wapUsuario->value;
    }

    /* Guarda un formulario */
    public function store($res)
    {
        $wapUsuario = new WapUsuario();
        $wapUsuario->set($res);
        return $wapUsuario->save();
    }

    /* Actualiza un form */
    public function update($req, $id)
    {
        $wapUsuario = new WapUsuario();
        return $wapUsuario->update($req, $id);
    }

    /* Actualiza un form */
    public function delete($id)
    {
        $wapUsuario = new WapUsuario();
        return $wapUsuario->delete($id);
    }
}
