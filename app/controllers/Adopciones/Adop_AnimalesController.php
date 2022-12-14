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

    public static function indexEverything($param = [], $ops = [])
    {
        $encodedData = [];
        $data = new Adop_Animal();

        $data = $data->list($param, $ops)->value;

        foreach ($data as $animal) {
            $apiPath = "";

            if ($_ENV['ENV'] == "local") {
                $apiPath = LOCAL_API_FETCH_PATH;
            } else if ($_ENV['ENV'] == "replica") {
                $apiPath = REPLICA_API_FETCH_PATH;
            } else if ($_ENV['ENV'] == "produccion") {
                $apiPath = WEBLOGIN_API_FETCH_PATH;
            }

            $animal['imagen1_path'] = $apiPath . "$animal[id]/$animal[imagen1_path]";
            $animal['imagen2_path'] = $apiPath . "$animal[id]/$animal[imagen2_path]";
            array_push($encodedData, $animal);
        }
        return $encodedData;
    }

    public static function indexAnimalPhotos($param = [], $ops = [])
    {
        $encodedData = [];
        $data = new Adop_Animal();
        $data = $data->list($param, $ops)->value;
        foreach ($data as $animal) {
            $apiPath = FETCH_PATH;

            if ($_ENV['ENV'] == "local") {
                $apiPath = LOCAL_API_FETCH_PATH;
            } else if ($_ENV['ENV'] == "replica") {
                $apiPath = REPLICA_API_FETCH_PATH;
            } else if ($_ENV['ENV'] == "produccion") {
                $apiPath = WEBLOGIN_API_FETCH_PATH;
            }

            $animal['imagen1_path'] = $apiPath . "$animal[id]/$animal[imagen1_path]";
            $animal['imagen2_path'] = $apiPath . "$animal[id]/$animal[imagen2_path]";

            $encodedData = [
                'imagen1_path' => $animal['imagen1_path'],
                'imagen2_path' => $animal['imagen2_path']
            ];
        }

        return $encodedData;
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
