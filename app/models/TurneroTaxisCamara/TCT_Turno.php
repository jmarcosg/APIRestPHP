<?php

namespace App\Models\TurneroTaxisCamara;

use App\Models\BaseModel;

class TCT_Turno extends BaseModel
{
    protected $table = 'TCT_turnos';
    protected $logPath = 'v1/turnero_taxis_camara';
    protected $identity = 'id';

    protected $fillable = [
        'fecha_id',
        'usuario_id',
        'turno' // M-T
    ];
}
