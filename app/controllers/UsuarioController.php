<?php

class UsuarioController
{
    /* Guarda un formulario */
    public function store($res)
    {
        $form = new Usuario();
        $form->set(...array_values($res));
        return $form->save();
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
