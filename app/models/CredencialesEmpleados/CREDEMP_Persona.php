<?php

namespace App\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Persona extends BaseModel
{
    protected $table = 'CREDEMP_personas';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'genero',
        'legajo',
        'cargo',
        'deshabilitado',
        'id_template'
    ];
}
