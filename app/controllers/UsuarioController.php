<?php

namespace App\Controllers;


use App\Models\Usuario;

class UsuarioController
{
    /* Guarda un formulario */
    public function store($res)
    {
        $usuario = new Usuario();
        $usuario->set($res);
        $id = $usuario->save();
        return $usuario->get(['ReferenciaID' => $id]);
    }

    public function index($param = [], $ops = [])
    {
        $usuario = new Usuario();
        return $usuario->list($param, $ops);
    }

    /* Busca un usuario */
    public function get($params)
    {
        $usuario = new Usuario();
        return $usuario->get($params);
    }

    /* Actualiza un form */
    public function update($table, $res, $id, $column)
    {
        $usuario = new Usuario();
        return $usuario->update($table, $res, $id, $column);
    }
}
