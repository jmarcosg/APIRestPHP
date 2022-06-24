<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

class Arb_Audit extends BaseModel
{
    protected $table = 'arb_audit';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_usuario',
        'id_wappersonas',
        'id_podador',
        'id_solicitud',
        'id_evaluacion',
        'id_inspector',
        'accion',
        'observacion',
    ];
}
