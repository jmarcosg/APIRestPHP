<?php

namespace App\Models\Common;

use App\Models\BaseModel;

class TipoDocumento extends BaseModel
{
    protected $table = 'lc_rubros';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';

    protected $fillable = [
        'nombre', 'codigo', 'formato', 'descripcion'
    ];
}
