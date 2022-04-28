<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

class Arb_Archivo extends BaseModel
{
    protected $table = 'arb_archivos';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = ['id_solicitud', 'name'];
}
