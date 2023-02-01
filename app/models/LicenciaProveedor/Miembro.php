<?php

namespace App\Models\LicenciaProveedor;

use App\Models\BaseModel;

class Miembro extends BaseModel
{
    protected $table = 'pro_miembros';
    protected $logPath = 'v1/proveedores';
    protected $identity = 'id';

    protected $fillable = [
        'id_solicitud',
        'nombre',
        'razon_social',
    ];
}
