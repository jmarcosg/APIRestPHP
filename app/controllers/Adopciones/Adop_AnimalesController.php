<?php

namespace App\Controllers\Adopciones;

use App\Models\Adopciones\Adop_Animal;

class Adop_AnimalesController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'ADOP_Animales';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Adop_Animal();
        $data = $data->list($param, $ops)->value;
        print_r($data);
        die();
        return $data;
    }

    public static function store($res)
    {
        $data = new Adop_Animal();
        $data->set($res);
        return $data->save();
    }

    public static function storeImages($file, $path, $animal, $imagen)
    {
        /* Agarramos la extension del archivo  */
        $fileExt = getExtFile($file);

        /* Borramos la carpeta del docuemento si existe */
        deleteDir(FILE_PATH . "adopciones/$path/");

        /* Copiamos el archivo */
        $copiado = copy($file['tmp_name'], $path . $fileExt);
        $url = null;

        if ($copiado) {
            $animal = new Adop_Animal();
            $params = [];
            $idAnimal = $animal->get($params)->value['id'];
            $animal->update([$imagen => $path . $fileExt], $idAnimal);
            $url = $animal->get($params)->value[$imagen];
        }

        if ($url) {
            sendRes(['url' => getBase64String($url, $url)]);
        } else {
            sendRes(null, 'Hubo un error al querer subir un archivo');
        };

        exit;
    }
}
