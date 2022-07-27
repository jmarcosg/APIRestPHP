<?php

namespace App\Models\LicenciaComercial;

use App\Models\BaseModel;

class Lc_Rubro extends BaseModel
{
    protected $table = 'lc_rubros';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';

    protected $fillable = [
        'codigo', 'nombre'
    ];

    protected $filesUrl = FILE_PATH . 'Licencia_comercial/solicitud/';
}
