<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

class Arb_Evaluacion extends BaseModel
{
    protected $table = 'arb_evaluaciones';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_wappersonas',
        'id_podador',
        'dni',
        'nombre',
        'fecha_evaluacion',
    ];
}
