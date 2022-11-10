<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\WlFotoPerfil;

class WlFotoPerfilController
{

    public static function getLastFotos()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $data = $wlFotoPerfil->get($_POST, ['order' => ' ORDER BY id DESC'])->value;

        if ($data) {
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
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

        /* if ($doc['documento']) {
            $codigo = $doc['codigo'];
            $url = $filesUrl . $id_solicitud . "/" . $codigo . '/' .  $doc['documento'];
            $data[$key]['url'] = getBase64String($url, $doc['documento']);
            $data[$key]['loading'] = false;
            $data[$key]['error'] = false;
        } */

        sendRes($registro);

        exit;
    }

    public static function saveFoto()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $wlFotoPerfil->saveFotos();

        $wlFotoPerfil->set($_POST);

        $id = $wlFotoPerfil->save();

        sendResError($id, 'Hubo un error al guardar la foto');

        $data = $wlFotoPerfil->get(['id' => $id])->value;

        if ($data) {
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
        } else {
            sendRes(null, 'No se encontraron registros');
        }

        exit;
    }
}
