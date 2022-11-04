<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_ReferenteBarrio extends BaseModel
{
    protected $table = 'MAPCIRC_referente_barrio';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'id_barrio',
        'id_persona',
    ];
}
