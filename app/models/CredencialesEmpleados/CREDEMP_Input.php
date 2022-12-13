<?php

namespace APP\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Input extends BaseModel
{
    protected $table = 'CREDEMP_inputs';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'id_template',
        'id_tipo',
        'name',
        'label',
        'max_length',
        'min_length',
        'min_number',
        'max_number',
        'regex',
        'required'
    ];
}
