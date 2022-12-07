<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_Administrador extends BaseModel
{
    protected $table = 'MAPCIRC_administrador';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'usuario',
        'clave',
        'deshabilitado',
        'fecha_deshabilitado'
    ];
}
