<?php

namespace App\Models\QRIdentificacion;

use App\Models\BaseModel;

class QRI_Persona extends BaseModel
{
    protected $table = 'QRI_personas';
    protected $logPath = 'v1/qr_identificacion';
    protected $identity = 'id';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'cargo',
        'telefono_alternativo',
        'lugar_trabajo'
    ];
}
