<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\WlFotoPerfil;

class WlFotoPerfilController
{
    public static function getLastFotos()
    {
        $data = new WlFotoPerfil();

        $registro = $data->get($_GET, ['order' => ' ORDER BY id DESC'])->value;

        if ($registro) {
            sendRes($registro);
        } else {
            sendRes(null, 'No se encontraron registros');
        }

        exit;
    }

    public static function getLastFotosPost()
    {
        $data = new WlFotoPerfil();

        $registro = $data->get($_POST, ['order' => ' ORDER BY id DESC'])->value;

        if ($registro) {
            sendRes($registro);
        } else {
            sendRes(null, 'No se encontraron registros');
        }

        exit;
    }

    public static function getFotoById()
    {
        $data = new WlFotoPerfil();

        $id = $_GET['id'];
        $registro = $data->get(['id' => $id])->value;

        sendRes($registro);

        exit;
    }

    public static function saveFoto()
    {
        $data = new WlFotoPerfil();

        $data->saveFotos();

        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Hubo un error al guardar la foto');

        sendRes($id);

        exit;
    }
}
