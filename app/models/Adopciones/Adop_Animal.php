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

    public $filesUrl = FILE_PATH . 'adopciones/animales/';

    public static function storeImages($file, $id, $animal, $imagen)
    {
        $adop_animal = new Adop_Animal();
        /* Agarramos la extension del archivo  */
        $fileExt = getExtFile($file);

        /* Borramos la carpeta del docuemento si existe */
        deleteDir(FILE_PATH . "adopciones/animales/$id/");

        /* Copiamos el archivo */
        $copiado = copy($file['tmp_name'], self::$filesUrl . $fileExt);
        $url = null;

        if ($copiado) {
            $animal = new Adop_Animal();
            $params = [];
            $animal->update([$imagen => 'nombreArchivos'], $id);
            $url = $animal->get(['id' => $id])->value;
            $url = self::$filesUrl . $url[$imagen];
        }

        if ($url) {
            sendRes(['url' => getBase64String($url, $url)]);
        } else {
            sendRes(null, 'Hubo un error al querer subir un archivo');
        };

        exit;
    }

    // public function saveAnimal($idSolicitud, $solicitud)
    // {
    //     $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 1, 'verificado' => 0];
    //     $this->set($params);
    //     $this->save();
    // }
}
