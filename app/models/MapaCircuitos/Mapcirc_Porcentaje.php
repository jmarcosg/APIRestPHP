<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_Porcentaje extends BaseModel
{
    protected $table = 'MAPCIRC_porcentaje';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'total',
        'anio',
        'diferenciador'
    ];
}
