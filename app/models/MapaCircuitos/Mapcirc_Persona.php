<?php

namespace App\Models\MapaCircuitos;

use App\Models\BaseModel;

class Mapcirc_Persona extends BaseModel
{
    protected $table = 'MAPCIRC_persona';
    protected $logPath = 'v1/mapacircuitos';
    protected $identity = 'id';

    protected $fillable = [
        'nombre',
        'tematica',
    ];
}
