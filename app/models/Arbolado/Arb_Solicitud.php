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

    function wapPersona()
    {
        return $this->hasOne(WapPersona::class, 'id_wappersonas',  'ReferenciaID');
    }

    function archivos()
    {
        $colleccion = $this->hasMany(Arb_Archivo::class, 'id',  'id_solicitud');

        if (isset($colleccion->value['archivos'])) {
            $archivos = $colleccion->value['archivos'];
            foreach ($archivos as $key => $archivo) {
                $path = $this->filesUrl . $colleccion->value['id'] . '/' . $archivo['name'];
                $colleccion->value['archivos'][$key]['path'] = $path;
            }
        }


        return $colleccion;
    }
}
