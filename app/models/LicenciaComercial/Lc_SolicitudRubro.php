<?php

namespace App\Models\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\BaseModel;

class Lc_SolicitudRubro extends BaseModel
{
    protected $table = 'lc_solicitud_rubros';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';

    protected $fillable = [
        'id_solicitud', 'id_solicitud_historial', 'codigo', 'principal'
    ];

    protected $filesUrl = FILE_PATH . 'Licencia_comercial/solicitud/';

    public static function rubrosUpdate($req, $id)
    {
        $rubros = explode(",", $req['rubros']);
        unset($req['rubros']);

        /* Borramos los rubros viejos */
        self::deleteBySolicitudId($id);

        /* Actualizamos los nuevos rubros */
        $rubro = new Lc_SolicitudRubro();
        $rubro->set(['id_solicitud' => $id, 'codigo' => $rubros[0], 'principal' => 1]);
        $rubro->save();

        unset($rubros[0]);
        foreach ($rubros as $r) {
            $rubro->set(['id_solicitud' => $id, 'codigo' => $r]);
            $rubro->save();
        }
    }

    public static function deleteBySolicitudId($id)
    {
        $conn = new BaseDatos();
        $conn->delete('lc_solicitud_rubros', ['id_solicitud' => $id]);
    }
}
