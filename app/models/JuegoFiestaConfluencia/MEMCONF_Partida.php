<?php

namespace App\Models\JuegoFiestaConfluencia;

use App\Models\BaseModel;

class MEMCONF_Partida extends BaseModel
{
    protected $table = 'MEMCONF_Partida';
    protected $logPath = 'v1/juegofiestaconfluencia';
    protected $identity = 'id';

    protected $fillable = [
        'id_usuario',
        'id_configuracion',
        'aciertos',
        'movimientos_totales',
        'gano',
        'fecha_jugada'
    ];
}
