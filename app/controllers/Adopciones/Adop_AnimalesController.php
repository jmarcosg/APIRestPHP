<?php

namespace App\Controllers\Adopciones;

use App\Models\Adopciones\Adop_Animal;

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

    public static function storeImages($file, $id, $animal, $imagenPath)
    {
        Adop_Animal::storeImages($file, $id, $animal, $imagenPath);
        /* Agarramos la extension del archivo  */
    }
}
