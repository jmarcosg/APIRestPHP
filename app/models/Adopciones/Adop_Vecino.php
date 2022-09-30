<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;

class Adop_Animal extends BaseModel
{
    protected $table = 'adop_animales';
    protected $logPath = 'v1/adopciones';
    protected $identity = 'id';

    protected $fillable = [
        'imagen1',
        'imagen2',
        'nombre',
        'edad',
        'raza',
        'tamanio',
        'castrado',
        'descripcion',
        'adoptado',
        'fecha_ingreso',
        'fecha_egreso'
    ];

    public $filesUrl = FILE_PATH . 'adopciones/nuevo/';
}
