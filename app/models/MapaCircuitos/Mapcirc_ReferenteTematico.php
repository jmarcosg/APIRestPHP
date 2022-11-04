<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_ReferenteTematico extends BaseModel
{
    protected $table = 'MAPCIRC_referente_tematico';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'id_circuito',
        'id_persona',
    ];
}
