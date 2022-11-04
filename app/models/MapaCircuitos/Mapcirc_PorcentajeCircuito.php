<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_PorcentajeCircuito extends BaseModel
{
    protected $table = 'MAPCIRC_porcentaje_circuito';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'id_circuito',
        'id_porcentaje',
    ];
}
