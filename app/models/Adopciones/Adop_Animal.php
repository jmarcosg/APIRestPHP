<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;

class Adop_Animal extends BaseModel
{
    protected $table = 'adop_animales';
    protected $logPath = 'v1/adopciones';
    protected $identity = 'id';

    protected $fillable = [
        'imagen1_path',
        'imagen2_path',
        'nombre',
        'edad',
        'raza',
        'tamanio',
        'castrado',
        'descripcion',
        'adoptado',
        'fecha_ingreso',
        'fecha_modificacion',
    ];

    public $filesUrl = FILE_PATH . 'adopciones/';

    // public function saveAnimal($idSolicitud, $solicitud)
    // {
    //     $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 1, 'verificado' => 0];
    //     $this->set($params);
    //     $this->save();
    // }
}
