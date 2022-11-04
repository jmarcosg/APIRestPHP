<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_Circuito extends BaseModel
{
    protected $table = 'MAPCIRC_barrio';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'nombre',
        'cantidad_electores'
    ];
}
