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
        'telefono',
        'observacion',
        'estado',
        'fecha_vencimiento',
        'fecha_revision',
        'id_usuario_admin',
        'id_wappersonas_admin',
        'fecha_deshabilitado',
        'motivo_deshabilitado'
    ];

    protected $filesUrl = FILE_PATH . 'Arbolado/podador/';

    function certificado($certificado, $id)
    {
        if ($certificado) {
            $path = $this->filesUrl . $id . '/' . $certificado;

            return [
                'name' => $certificado,
                'path' => getBase64String($path, $certificado),
            ];
        }
        return null;
    }
}
