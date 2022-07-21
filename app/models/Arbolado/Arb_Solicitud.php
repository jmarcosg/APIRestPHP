<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

use App\Models\WapPersona;

class Arb_Solicitud extends BaseModel
{
    protected $table = 'arb_solicitudes';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_usuario',
        'id_wappersonas',
        'tipo',
        'solicita',
        'ubicacion',
        'motivo',
        'cantidad',
        'contacto',
        'estado',
        'observacion',
        'id_inspector',
        'cantidad_autorizado',
        'cantidad_reponer',
        'dias_reponer',
        'especie',
        'constancia_danio',
        'observacion_inspector',
    ];

    protected $filesUrl = FILE_PATH . 'Arbolado/solicitud_poda/';

    function archivos($id)
    {
        $archivo = new Arb_Archivo();
        $archivos = $archivo->list(['id_solicitud' => $id])->value;

        foreach ($archivos as $key => $archivo) {
            $path = $this->filesUrl . $id . '/' . $archivo['name'];
            $archivos[$key]['path'] = base64_encode(file_get_contents($path));
        }

        return $archivos;
    }
}
