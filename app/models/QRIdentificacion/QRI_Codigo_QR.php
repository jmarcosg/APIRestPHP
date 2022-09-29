<?php

namespace App\Models\QRIdentificacion;

use App\Models\BaseModel;

class QRI_Codigo_QR extends BaseModel
{
    protected $table = 'QRI_codigos_qr';
    protected $logpath = 'v1/qr_identificacion';
    protected $identity = 'id';

    protected $fillable = [
        'id_usuario',
        'id_persona_identificada',
        'qr_path',
        'qr_token'
    ];

    public $filesUrl = FILE_PATH . 'qr_identificacion/codigo/';
}
