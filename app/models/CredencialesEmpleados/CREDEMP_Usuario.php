<?php

namespace APP\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Usuario extends BaseModel
{
    protected $table = 'CREDEMP_usuarios';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email'
    ];
}
