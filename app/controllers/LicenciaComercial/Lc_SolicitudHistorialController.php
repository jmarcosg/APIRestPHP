<?php

namespace App\Controllers\LicenciaComercial;

use App\Models\LicenciaComercial\Lc_SolicitudHistorial;
use App\Models\LicenciaComercial\Lc_Documento;

use App\Traits\LicenciaComercial\FormatTrait;
use App\Traits\LicenciaComercial\QuerysSql;
use ErrorException;

class Lc_SolicitudHistorialController
{
    use QuerysSql, FormatTrait;

    public static function store()
    {
        /* Guardamos la solicitud */
        $_POST['estado'] = 'act';
        $data = new Lc_SolicitudHistorial;
        $data->set($_POST);
        $id = $data->save();

        /* Guardamos un registro de reserva para los documentos */
        $documento = new Lc_Documento();
        $documento->set(['id_solicitud' => $id]);
        $documento->save();

        if (!$id instanceof ErrorException) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, $id->getMessage(), $_GET);
        };
        exit;
    }

    public static function setViewHistorial($id)
    {
        $data = new Lc_SolicitudHistorial;

        $data = $data->update(['visto' => '1'], $id);

        sendResError($data, 'No se pudo macar como visto el historial');

        sendRes(['id' => $id]);
    }

    public static function getHistorialByQuery($where)
    {
        $solicitud = new Lc_SolicitudHistorial();

        $sql = self::getSqlHistorial($where);
        $data = $solicitud->executeSqlQuery($sql, false);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public static function getHistorialBySol($id)
    {
        $solicitud = new Lc_SolicitudHistorial();

        $sql = self::getSqlHistorial("id_solicitud = $id");
        $data = $solicitud->executeSqlQuery($sql, false);

        sendRes($data);
        exit;
    }

    public function delete($id)
    {
        $data = new Lc_SolicitudHistorial;
        return $data->delete($id);
    }
}
