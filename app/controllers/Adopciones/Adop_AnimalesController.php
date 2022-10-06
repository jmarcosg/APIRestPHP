<?php

namespace App\Controllers\Adopciones;

use App\Connections\BaseDatos;
use App\Models\Adopciones\Adop_Animal;
use ErrorException;

class Adop_AnimalesController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'ADOP_animales';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Adop_Animal();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new Adop_Animal();
        $data->set($res);
        return $data->save();
    }

    public static function storeImage($file, $id, $imagenPath)
    {
        $data = new Adop_Animal();
        return $data->storeImage($file, $id, $imagenPath);
    }

    public static function update($req, $id)
    {
        $data = new Adop_Animal();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Adop_Animal();
        return $data->delete($id);
    }
}
