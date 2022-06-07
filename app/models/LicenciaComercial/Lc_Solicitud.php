<?php

namespace App\Models\LicenciaComercial;

use App\Models\BaseModel;

class Lc_Solicitud extends BaseModel
{
    protected $table = 'lc_solicitudes';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_athandlerChangeRubro';

    protected $fillable = [
        'id_documento',
        'id_usuario',
        'id_wappersonas',
        'pertenece',
        'id_wappersonas_tercero',
        'tipo_persona',
        'tiene_local',
        'nomenclatura',
        'observacion',
        'estado'
    ];

    protected $filesUrl = FILE_PATH . 'Licencia_comercial/solicitud/';
}
