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

    protected $fillable = ['id_usuario', 'id_wappersonas', 'tipo', 'solicita', 'ubicacion', 'motivo', 'cantidad', 'contacto'];

    function wapUsuario()
    {
        return $this->hasOne(WapPersona::class, 'id_wappersonas',  'ReferenciaID');
    }
}
