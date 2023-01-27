<?php

namespace App\Controllers\Proveedor;

use App\Models\LicenciaProveedor\Proveedor;
use App\Traits\Proveedor\Validaciones;

class Pro_SolicitudController
{
    use Validaciones;
    public static function getSolicitudesUser()
    {
        $id = $_POST['id_usuario'];
        $data = new Proveedor();

        $solicitudes = $data->list(['id_usuario' => $id])->value;

        sendResError($id, 'Problema para obtener las solicitudes', $data->req);

        if (count($solicitudes)) {
            sendRes($solicitudes);
        } else {
            sendRes(null, 'No se encontraron solicitudes', $_GET);
        }
    }

    public static function saveTipoPersona()
    {
        self::checkParams(__FUNCTION__);

        $data = new Proveedor();
        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Problema al guardar el registro - intente nuevamente mas tarde', $data->req);

        $solicitud = $data->get(['id' => $id])->value;
        sendResError($id, 'Problema al obtener el registro - deberá recargar el sistema', $data->req);

        sendRes($solicitud);
    }
    public static function saveDatosComerciales()
    {
        self::checkParams(__FUNCTION__);

        $data = new Proveedor();
        $data->set($_POST);
        $id = $_POST['id_solicitud'];

        $solicitud = $data->get(['id' => $id])->value;

        sendResError($solicitud, 'Problema al guardar el registro - intente nuevamente mas tarde', $data->req);

        $update = $data->update($_POST, $id);

        sendResError($solicitud, 'Problema al guardar el registro - intente nuevamente mas tarde', $data->req);

        if ($update) {
            $solicitud = $data->get(['id' => $id])->value;
        } else {
            sendRes(null, 'Problema al actualizar el registro');
        }

        $solicitud = $data->get(['id' => $id])->value;

        sendResError($id, 'Problema al obtener el registro - deberá recargar el sistema', $data->req);

        sendRes($solicitud);
    }
}
