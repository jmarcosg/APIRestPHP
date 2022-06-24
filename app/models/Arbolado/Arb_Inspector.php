<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

class Arb_Inspector extends BaseModel
{
    protected $table = 'arb_inspectores';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'dni',
        'legajo',
        'nombre',
    ];
}
