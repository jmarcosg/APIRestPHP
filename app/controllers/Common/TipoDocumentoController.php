<?php

namespace App\Controllers\Common;

use App\Connections\BaseDatos;
use App\Models\Common\TipoDocumento;
use ErrorException;

class TipoDocumentoController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_solicitud_rubros';
    }

    public static function index()
    {
        $data = new TipoDocumento();
        $sql =
            "SELECT 
                id as value,
                nombre as label
            FROM dbo.tipos_documentos
            WHERE id > 10";

        $data = $data->executeSqlQuery($sql, false);

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public function get($params)
    {
        $data = new TipoDocumento();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new TipoDocumento();
        $data->set($res);
        return $data->save();
    }

    public function updateFirts($req, $id)
    {
        $data = new TipoDocumento();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new TipoDocumento();
        return $data->delete($id);
    }
}
