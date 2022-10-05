<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;
use ErrorException;

class Adop_Animal extends BaseModel
{
    protected $table = 'ADOP_animales';
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
        'deshabilitado',
        'fecha_ingreso',
        'fecha_modificacion',
        'fecha_deshabilitado',
    ];

    public $filesUrl = FILE_PATH . 'adopciones\\animales\\';

    public function storeImage($file, $id, $imagenPath)
    {
        /* Copiamos el archivo y creamos su directorio */
        $tmpFile = $file['tmp_name'];

        $fileUrl = $this->filesUrl . "$id\\$imagenPath";
        // $fileFolder = $this->filesUrl . "$id\\";
        // $folderCreated = mkdir("$fileFolder");

        $result = copy($tmpFile, $fileUrl);

        if (!$result || $result instanceof ErrorException) {
            return false;
        }
        return $result;
    }
}
