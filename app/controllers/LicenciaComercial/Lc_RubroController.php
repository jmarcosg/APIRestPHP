<?php

namespace App\Controllers\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\LicenciaComercial\Lc_Rubro;

class Lc_RubroController
{

    public static function getAllRubros()
    {
        $data = new Lc_Rubro();
        $sql =
            "SELECT 
                codigo as value,
                (select cast(codigo as varchar) + ' - ' + nombre) as label	
            FROM dbo.lc_rubros";

        $data = $data->executeSqlQuery($sql, false);

        sendResError($data, 'Problema para obtener los rubros');
        sendRes($data);
    }

    public function get($params)
    {
        $data = new Lc_Rubro();
        $data = $data->get($params)->value;
        return $data;
    }

    public function delete($id)
    {
        $data = new Lc_Rubro();
        return $data->delete($id);
    }

    public function deleteBySolicitudId($id)
    {
        $conn = new BaseDatos();
        $result = $conn->delete('lc_rubros', ['id_solicitud' => $id]);
    }
}
