<?php

namespace App\Controllers\Proveedor;

use App\Models\LicenciaProveedor\Miembro;
use App\Models\LicenciaProveedor\Proveedor;
use App\Traits\LicenciaProveedor\Validaciones;

class Pro_SolicitudController
{
    use Validaciones;

    /** Obtiene todas las solicitudes del usuario */
    public static function getSolicitudesUser()
    {
        $id_usuario = $_POST['id_usuario'];
        $data = new Proveedor();

        $solicitudes = $data->list(['id_usuario' => $id_usuario])->value;

        sendResError($id_usuario, 'Problema para obtener las solicitudes', $data->req);

        if (count($solicitudes)) {
            sendRes($solicitudes);
        } else {
            sendRes(null, 'No se encontraron solicitudes', $_GET);
        }
    }

    /** Obtiene una solicitud puntual del usuario */
    public static function getSolicitudUser()
    {
        $id = $_POST['id'];
        $id_usuario = $_POST['id_usuario'];

        $data = new Proveedor();

        $solicitud = $data->get(['id' => $id, 'id_usuario' => $id_usuario])->value;

        if (!$solicitud['condicion_iva']) $solicitud['condicion_iva'] = 'DEFAULT';
        if (!$solicitud['naturaleza_juridica']) $solicitud['naturaleza_juridica'] = 'DEFAULT';

        sendResError($solicitud, 'Problema para obtener las solicitudes', $_POST);

        if (!$solicitud) sendRes(null, "No se encontro la solicitud $id");

        sendRes($solicitud);
    }

    /** Se guarda la primera parte de la solicitud - Datos personales */
    public static function saveTipoPersona()
    {
        self::checkParams(__FUNCTION__);

        $data = new Proveedor();
        $_POST['estado'] = 'comercial';
        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Problema al guardar el registro - intente nuevamente mas tarde', $data->req);

        $solicitud = $data->get(['id' => $id])->value;
        sendResError($id, 'Problema al obtener el registro - deberá recargar el sistema', $data->req);

        sendRes($solicitud);
    }

    /** Guardamos la segunda parte de la solicitud - Datos Comerciales */
    public static function saveDatosComerciales()
    {
        $id = $_POST['id_solicitud'];
        $data = new Proveedor();
        $solicitud = $data->get(['id' => $id])->value;

        self::checkParams(__FUNCTION__);

        $_POST['estado'] = 'notificaciones';
        $data->set($_POST);

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

    public static function getMiembros()
    {
        $id_solicitud = $_POST['id_solicitud'];
        $data = new Miembro();

        $miembros = $data->list(['id_solicitud' => $id_solicitud])->value;

        sendResError($miembros, 'Problema para obtener las solicitudes', $data->req);

        if (count($miembros)) {
            sendRes($miembros);
        } else {
            sendRes(null, 'No se encontraron miembros', $_GET);
        }
    }

    public static function agregarMiembro()
    {
        self::checkParams(__FUNCTION__);

        $data = new Miembro();

        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Problema al guardar el registro - intente nuevamente mas tarde', $data->req);

        $solicitud = $data->get(['id' => $id])->value;

        sendResError($id, 'Problema al obtener el registro - deberá recargar el sistema', $data->req);

        sendRes($solicitud);
    }
}
