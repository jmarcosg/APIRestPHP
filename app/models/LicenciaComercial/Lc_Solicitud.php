<?php

namespace App\Models\LicenciaComercial;

use App\Models\BaseModel;
use App\Models\WapPersona;

class Lc_Solicitud extends BaseModel
{
    protected $table = 'lc_solicitudes';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_usuario',
        'id_wappersonas',
        'telefono',
        'correo',
        'pertenece',
        'id_wappersonas_tercero',
        'dni_tercero',
        'tramite_tercero',
        'genero_tercero',
        'tipo_persona',
        'cuit',
        'tiene_local',
        'nomenclatura',
        'm2',
        'descripcion_actividad',
        'estado',
        'observacion',
        'ver_rubros',
        'ver_catastro',
        'ver_ambiental',
        'ver_documentos',
    ];

    protected $filesUrl = FILE_PATH . 'Licencia_comercial/solicitud/';
}
