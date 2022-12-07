<?php

namespace App\Models\CredencialesEmpleados;

use App\Models\BaseModel;

class CREDEMP_Codigo_QR extends BaseModel
{
    protected $table = 'CREDEMP_codigos_qr';
    protected $logPath = 'v1/credenciales_empleados';
    protected $identity = 'id';

    protected $fillable = [
        'id_usuario',
        'id_persona_identificada',
        'qr_path',
        'qr_local_path',
        'qr_token'
    ];

    public $filesUrl = FILE_PATH . 'credenciales-empleados/codigo/';
}
