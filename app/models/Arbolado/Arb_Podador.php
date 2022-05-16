<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

use App\Models\WapPersona;
use App\Controllers\Arbolado\Arb_EvaluacionController;

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
        'observacion',
        'estado',
        'fecha_evaluacion',
        'fecha_vencimiento'
    ];

    protected $filesUrl = 'http://localhost/APIrest/files/Arbolado/podador/';

    function wapPersona()
    {
        return $this->hasOne(WapPersona::class, 'id_wappersonas',  'ReferenciaID');
    }

    function certificado()
    {
        if (isset($this->value['certificado'])) {
            $name = $this->value['certificado'];
            $this->value['certificado'] = [
                'name' => $name,
                'path' => $this->filesUrl . $this->value['id'] . '/' . $name,
            ];
        }
    }
}
