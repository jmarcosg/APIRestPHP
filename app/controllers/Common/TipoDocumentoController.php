<?php

namespace App\Controllers\Common;

use App\Models\Common\TipoDocumento;

class TipoDocumentoController
{
    public static function getAllTiposDocumentos()
    {
        $data = new TipoDocumento();
        $sql =
            "SELECT 
                id as value,
                nombre as label,
                codigo as codigo,
                requiere as req
            FROM dbo.tipos_documentos
            WHERE id > 10";

        $data = $data->executeSqlQuery($sql, false);

        sendResError($data, 'Problema para obtener los tipos de documentos');

        sendRes($data);
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

    public function delete($id)
    {
        $data = new TipoDocumento();
        return $data->delete($id);
    }
}
