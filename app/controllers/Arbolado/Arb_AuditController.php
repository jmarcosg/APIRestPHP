<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Audit;

class Arb_AuditController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_audit';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Arb_Audit();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function indexRes($where)
    {
        $audit = new Arb_Audit();

        $sql =
            "SELECT 
            aud.id as id,
            per.Nombre as nombre,
            per.Documento as documento       
        FROM dbo.arb_audit aud
            LEFT JOIN dbo.wapPersonas per ON aud.id_wappersonas = per.ReferenciaID  
        where $where AND aud.deleted_at IS NULL
        ORDER BY id DESC";

        $data = $audit->executeSqlQuery($sql, false);

        if (count($data) > 0) {
            return $data[0];
        }
        return null;
    }

    public function get($params)
    {
        $data = new Arb_Audit();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Arb_Audit();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Audit();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Audit();
        return $data->delete($id);
    }
}
