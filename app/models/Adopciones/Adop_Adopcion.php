<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;

class Adop_Adopcion extends BaseModel
{
    protected $table = 'ADOP_adopciones';
    protected $logPath = 'v1/adopciones';
    protected $identity = 'id';

    protected $fillable = [
        'id_animal',
        'id_vecino',
        'fecha_adopcion'
    ];
}
