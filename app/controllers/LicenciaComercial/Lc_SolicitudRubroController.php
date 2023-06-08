<?php

namespace App\Controllers\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\LicenciaComercial\Lc_SolicitudRubro;

class Lc_SolicitudRubroController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_solicitud_rubros';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Lc_SolicitudRubro();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Lc_SolicitudRubro();
        $data = $data->get($params)->value;
        return $data;
    }

    public function getRubrosBySolicitud($id)
    {
        $sql =
            "SELECT 
            r.codigo as value,
            (select cast(sr.codigo as varchar) + ' - ' + r.nombre) as label	
        FROM dbo.lc_solicitud_rubros sr
            LEFT JOIN dbo.lc_rubros r ON sr.codigo = r.codigo
        WHERE sr.id_solicitud = $id";

        $rubro = new Lc_SolicitudRubro();
        return $rubro->executeSqlQuery($sql, false);
    }

    public function delete($id)
    {
        $data = new Lc_SolicitudRubro();
        return $data->delete($id);
    }

    public function deleteBySolicitudId($id)
    {
        $conn = new BaseDatos();
        $conn->delete('lc_solicitud_rubros', ['id_solicitud' => $id]);
    }
}
