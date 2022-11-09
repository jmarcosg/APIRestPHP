<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\WlFotoPerfil;

class WlFotoPerfilController
{
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
