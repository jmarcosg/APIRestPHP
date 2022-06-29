<?php

namespace App\Controllers\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\LicenciaComercial\Lc_Documento;

class Lc_DocumentoController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_rubro';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Lc_Documento();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Lc_Documento();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $data = new Lc_Documento();
        $data->set($res);
        return $data->save();
    }

    public static function update()
    {
        $id_solicitud = $_POST['id_solicitud'];

        $columnFile = $_POST['columnFile'];
        $file = $_FILES[$columnFile];

        $nameFile = uniqid() . getExtFile($file);
        $_POST[$columnFile] = $nameFile;

        /* Borramos la carpeta del docuemento si existe */
        deleteDir(FILE_PATH_LOCAL . "licencia_comercial/solicitud/$id_solicitud/$columnFile/");

        /* Generamops la carpeta y obtenemos el path para copiar el archivo */
        $path = getPathFile($file, FILE_PATH_LOCAL . "licencia_comercial/solicitud/$id_solicitud/$columnFile/", $nameFile);

        /* Capiamos el archivo */
        $copiado = copy($file['tmp_name'], $path);

        $url = null;
        if ($copiado) {
            $documento = new Lc_Documento();
            $idDocumento = $documento->get(['id_solicitud' => $id_solicitud])->value['id'];
            $documento->update([$columnFile => $nameFile], $idDocumento);

            $url = $documento->filesUrl . $id_solicitud . '/' . $columnFile . '/' . $nameFile;
        }

        if ($url) {
            sendRes(['url' => $url]);
        } else {
            sendRes(null, 'Hubo un error a subir un archivo', ['id' => $id_solicitud]);
        };
    }

    public function delete($id)
    {
        $data = new Lc_Documento();
        return $data->delete($id);
    }

    public function deleteBySolicitudId($id)
    {
        $conn = new BaseDatos();
        $result = $conn->delete('lc_rubros', ['id_solicitud' => $id]);
    }
}
