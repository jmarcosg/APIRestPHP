<?php

namespace App\Models\IdeasPropuestas;

use App\Models\BaseModel;

class IdeasPropuestas extends BaseModel
{
    protected $table = 'ip_ideas';
    protected $logPath = 'v1/ideas_propuestas';
    protected $identity = 'id';

    protected $fillable = [
        'id_usuario',
        'content',
    ];
}
