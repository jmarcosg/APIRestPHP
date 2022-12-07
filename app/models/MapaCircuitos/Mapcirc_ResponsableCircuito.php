<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_ResponsableCircuito extends BaseModel
{
    protected $table = 'MAPCIRC_responsable_circuito';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'id_circuito',
        'id_persona',
    ];
}
