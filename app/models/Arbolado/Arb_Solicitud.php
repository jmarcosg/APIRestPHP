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

    protected $fillable = ['id_usuario', 'id_wappersonas', 'tipo', 'solicita', 'ubicacion', 'motivo', 'cantidad', 'contacto', 'estado'];

    protected $filesUrl = 'http://localhost/APIrest/files/Arbolado/';

    function wapUsuario()
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
