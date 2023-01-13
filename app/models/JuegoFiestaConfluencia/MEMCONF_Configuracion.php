<?php

namespace App\Models\JuegoFiestaConfluencia;

use App\Models\BaseModel;

class MEMCONF_Configuracion extends BaseModel
{
    protected $table = 'MEMCONF_Configuracion';
    protected $logPath = 'v1/juegofiestaconfluencia';
    protected $identity = 'id';

    protected $fillable = [
        'descripcion',
        'tiempo',
        'activa',
    ];
}
