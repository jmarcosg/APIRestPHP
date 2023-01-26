<?php

namespace App\Controllers\Proveedor;

use App\Models\LicenciaProveedor\Proveedor;
use App\Traits\Proveedor\Validaciones;

class Pro_SolicitudController
{
    use Validaciones;
    public static function saveTipoPersona()
    {
        self::checkParams(__FUNCTION__);

        $data = new Proveedor();
        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Problema al guardar el registro - intente nuevamente mas tarde', $data->req);

        sendRes(['id' => $id]);
    }
}
