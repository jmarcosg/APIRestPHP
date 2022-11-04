<?php

namespace App\Models\IdeasPropuestas;

use App\Models\BaseModel;
use ErrorException;

class Usuarios extends BaseModel
{
    protected $table = 'ip_usuarios';
    protected $logPath = 'v1/ideas_propuestas';
    protected $identity = 'id';

    protected $fillable = [
        "nombre",
        "legajo",
        "usuario",
        "dni",
        "password",
        "tipo",
        "categoria",
        "secretaria",
        "subsecretaria",
        "info",
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setUniquesIndex();
    }
}
