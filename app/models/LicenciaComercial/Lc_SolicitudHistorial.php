<?php

namespace App\Models\LicenciaComercial;

use App\Models\BaseModel;
use App\Models\WapPersona;

class Lc_SolicitudHistorial extends BaseModel
{
    protected $table = 'lc_solicitudes_historial';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_solicitud',
        'id_documento',
        'id_usuario',
        'id_wappersonas',
        'pertenece',
        'id_wappersonas_tercero',
        'dni_tercero',
        'tramite_tercero',
        'genero_tercero',
        'tipo_persona',
        'tiene_local',
        'nomenclatura',
        'observacion',
        'estado'
    ];
}
