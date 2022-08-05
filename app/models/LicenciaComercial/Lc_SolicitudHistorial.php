<?php

namespace App\Models\LicenciaComercial;

use App\Models\BaseModel;

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
        'id_wappersonas_admin',
        'pertenece',
        'id_wappersonas_tercero',
        'dni_tercero',
        'tramite_tercero',
        'genero_tercero',
        'tipo_persona',
        'tiene_local',
        'nomenclatura',
        'descripcion_actividad',
        'estado',
        'observacion',
        'ver_rubros',
        'ver_catastro',
        'ver_ambiental',
        'tipo_registro',
        'ver_documentos',
        'visto'
    ];
}
