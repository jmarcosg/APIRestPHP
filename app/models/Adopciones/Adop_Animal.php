<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;

class Adop_Vecino extends BaseModel
{
    protected $table = 'ADOP_vecinos';
    protected $logPath = 'v1/adopciones';
    protected $identity = 'id';

    protected $fillable = [
        'nombre',
        'dni',
        'email',
        'email_alternativo',
        'telefono',
        'telefono_alternativo',
        'ciudad',
        'domicilio'
    ];
}
