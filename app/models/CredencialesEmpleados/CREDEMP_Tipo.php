<?php

namespace APP\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Tipo extends BaseModel
{
    protected $table = 'CREDEMP_tipos';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'descripcion',
        'tipo_html'
    ];
}
