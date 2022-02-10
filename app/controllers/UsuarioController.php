<?php

namespace App\Controllers;


use App\Models\Usuario;

class UsuarioController
{
    /* Guarda un formulario */
    public function store($res)
    {
        $form = new Usuario();
        $form->set(...array_values($res));
        return $form->save();
    }

    public static function index($param = [], $ops = [])
    {
        return Usuario::list($param, $ops);
    }

    /* Busca un usuario */
    static public function get($params)
    {
        return Usuario::get($params);
    }

    /* Actualiza un form */
    public static function update($table, $res, $id, $column)
    {
        return Usuario::update($table, $res, $id, $column);
    }
}
