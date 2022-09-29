<?php

namespace APP\Models\QRIdentificacion;

use App\Models\BaseModel;

class QRI_Usuario extends BaseModel
{
    protected $table = 'QRI_usuarios';
    protected $logPath = 'v1/qr_identificacion';
    protected $identity = 'id';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email'
    ];
}
