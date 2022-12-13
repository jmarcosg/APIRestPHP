<?php

namespace APP\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Template extends BaseModel
{
    protected $table = 'CREDEMP_templates';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'name',
        'descripcion',
        'visibilidad_dni',
        'visibilidad_nombre',
        'visibilidad_apellido',
        'visibilidad_legajo',
        'visibilidad_cargo',
        'needed_inputs',
        'deshabilitado'
    ];
}
