<?php

namespace App\Controllers;


use App\Models\DeportesUsuario;

class DeportesUsuarioController
{
    /* Buscar todos los usuarios */
    public function index($param = [], $ops = [])
    {
        $deportesUsuario = new DeportesUsuario();
        return $deportesUsuario->list($param, $ops);
    }

    /* Busca un usuario */
    public function get($params)
    {
        $deportesUsuario = new DeportesUsuario();
        return $deportesUsuario->get($params);
    }

    /* Guarda un formulario */
    public function store($res)
    {
        $deportesUsuario = new DeportesUsuario();
        $deportesUsuario->set($res);
        return $deportesUsuario->save();
    }

    /* Actualiza un form */
    public function update($req, $id)
    {
        $deportesUsuario = new DeportesUsuario();
        return $deportesUsuario->update($req, $id);
    }

    /* Actualiza un form */
    public function delete($id)
    {
        $deportesUsuario = new DeportesUsuario();
        return $deportesUsuario->delete($id);
    }
}
