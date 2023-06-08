<?php

namespace App\Models\JuegoFiestaConfluencia;

use App\Models\BaseModel;

class MEMCONF_Usuario extends BaseModel
{
    protected $table = 'MEMCONF_Usuario';
    protected $logPath = 'v1/juegofiestaconfluencia';
    protected $identity = 'id';

    protected $fillable = [
        'usuario_instagram'
    ];
}
