<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;

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
        print_r($file);
        die();
        /* Agarramos la extension del archivo  */
        $fileExt = getExtFile($file);

        /* Borramos la carpeta del docuemento si existe */
        deleteDir(FILE_PATH . "adopciones\\animales\\$id\\");

        /* Copiamos el archivo */
        $tmpFile = $file['tmp_name'];
        $fileCopied = copy($tmpFile, $this->filesUrl . "animal" . $fileExt);
        $url = null;

        if ($fileCopied) {
            $filesUrl = $this->filesUrl;
            $animal = new Adop_Animal();

            mkdir("$filesUrl\\$id\\");

            if ($imagenPath == "imagen1_path") {
                $fileName = "imagen-grande";
            } elseif ($imagenPath = "imagen2_path") {
                $fileName = "imagen-chica";
            }

            $animal->update([$imagenPath => $fileName . $fileExt], $id);
            $url = $animal->get(['id' => $id])->value;
            $url = $this->filesUrl . "$id\\" . $url[$imagenPath];
        }

        if ($url) {
            sendRes(['url' => getBase64String($url, $url)]);
        } else {
            sendRes(null, 'Hubo un error al querer subir un archivo');
        };

        exit;
    }
}
