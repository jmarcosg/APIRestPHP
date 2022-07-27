<?php

namespace App\Models\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\BaseModel;
use ErrorException;

class Lc_SolicitudRubro extends BaseModel
{
    protected $table = 'lc_solicitud_rubros';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';

    protected $fillable = [
        'id_solicitud', 'id_solicitud_historial', 'codigo', 'principal'
    ];

    protected $filesUrl = FILE_PATH . 'Licencia_comercial/solicitud/';
}
