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
        'tipo_edad',
        'es_de_raza',
        'tipo_raza',
        'raza',
        'tamanio',
        'castrado',
        'descripcion',
        'adoptado',
        'fecha_ingreso',
        'fecha_modificacion',
    ];


    public $filesUrl = FILE_PATH . 'adopciones\\animales\\';

    public function storeImage($file, $id, $imagenPath)
    {

        $fileType = $file['type'];

        $fileCopied = false;

        /* Agarramos la extension del archivo  */
        $fileExt = getExtFile($file);

        /* Renombramos el archivo según su id de formulario */
        if ($imagenPath == "imagen1_path") {
            $fileName = "imagen-grande";
        }
        if ($imagenPath == "imagen2_path") {
            $fileName = "imagen-chica";
        }

        /* Borramos la imagen si existe a partir de su extension */
        $i = 0;
        $allowedExt = [".jpeg", ".jpg", ".png", ".webp"];
        $fileExists = false;
        while (!$fileExists && $i < count($allowedExt)) {
            // $fileExists = file_exists(FILE_PATH . "adopciones\\animales\\$id\\$imagenPath" . $allowedExt[$i]);
            $fileExists = file_exists($this->filesUrl . "$id\\$imagenPath" . $allowedExt[$i]);

            if ($fileExists) {
                $fileExt = $allowedExt[$i];
                // deleteDir(FILE_PATH . "adopciones\\animales\\$id\\$imagenPath" . $fileExt);
                deleteDir($this->filesUrl . "$id\\$imagenPath" . $fileExt);
            }

            $i++;
        }

        $fileUrl = $fileName . $fileExt;
        $fileFolder = $this->filesUrl . "$id\\";
        $fileFolderWithFile = $this->filesUrl . "$id\\" . $fileName . $fileExt;

        $folderExists = file_exists($fileFolder);

        if (!$folderExists) {
            mkdir($fileFolder, 0777, true);
        }

        /* Copiamos el archivo y creamos su directorio */
        // $tmpFile = $file['tmp_name'];
        // $filePath = FILE_PATH . "adopciones\\animales\\$id\\";
        $filePath = $this->filesUrl . "\\$id\\";
        // $filePath = FILE_PATH . "adopciones\\animales\\$id\\$imagenPath" . $fileExt;
        $filePath = $this->filesUrl . "\\$id\\$fileUrl";

        /* Comprimimos la imagen */
        $compressedFile = comprimirImagen($file, $fileType, $filePath);

        if ($compressedFile && $fileFolderWithFile) {
            $animal = new Adop_Animal();
            $animal->update([$imagenPath => $fileUrl], $id);
            $fileCopied = $fileUrl;
        }

        return $fileCopied;
    }

    public function getPath()
    {
        return $this->filesUrl;
    }
}
