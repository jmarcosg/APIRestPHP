<?php

namespace App\Models\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\BaseModel;
use ErrorException;

class Lc_Rubro extends BaseModel
{
    protected $table = 'lc_rubros';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';

    protected $fillable = [
        'id_solicitud', 'nombre'
    ];

    protected $filesUrl = FILE_PATH . 'Licencia_comercial/solicitud/';
}
