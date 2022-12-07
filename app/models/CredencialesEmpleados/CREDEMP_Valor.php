<?php

namespace APP\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Valor extends BaseModel
{
    protected $table = 'CREDEMP_valores';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'id_input',
        'id_persona',
        'id_template',
        'valor'
    ];
}
