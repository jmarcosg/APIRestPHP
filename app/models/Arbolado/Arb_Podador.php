<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

use App\Models\WapPersona;

class Arb_Podador extends BaseModel
{
    protected $table = 'arb_podadores';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_usuario',
        'id_wappersonas',
        'certificado',
        'capacitador',
        'fecha_capacitacion',
        'verificado',
        'observacion',
        'estado',
        'fecha_evaluacion'
    ];

    protected $filesUrl = 'http://localhost/APIrest/files/Arbolado/podador/';

    function wapPersona()
    {
        return $this->hasOne(WapPersona::class, 'id_wappersonas',  'ReferenciaID');
    }
}
