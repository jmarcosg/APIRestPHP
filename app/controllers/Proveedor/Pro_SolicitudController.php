<?php

namespace App\Controllers\Proveedor;

use App\Models\LicenciaProveedor\Proveedor;
use App\Traits\Proveedor\Validaciones;

class Pro_SolicitudController
{
    use Validaciones;
    public static function saveSolicitud()
    {
        self::checkParams(__FUNCTION__);

        sendRes($_POST);
        $data = new Proveedor();
        $data->set($_POST);

        $id = $data->save();
        sendRes('ok');
    }
}
