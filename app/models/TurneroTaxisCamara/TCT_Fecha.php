<?php

namespace App\Models\TurneroTaxisCamara;

use App\Models\BaseModel;

class TCT_Fecha extends BaseModel
{
    protected $table = 'TCT_fechas';
    protected $logPath = 'v1/turnero_taxis_camara';
    protected $identity = 'id';

    protected $fillable = [
        'codigo'
    ];
}
