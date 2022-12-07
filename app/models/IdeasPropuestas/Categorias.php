<?php

namespace App\Models\IdeasPropuestas;

use App\Models\BaseModel;

class Categorias extends BaseModel
{
    protected $table = 'ip_categorias';
    protected $logPath = 'v1/ideas_propuestas';
    protected $identity = 'id';

    protected $fillable = [
        "nombre",
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setUniquesIndex();
    }
}
