<?php

namespace App\Controllers\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\LicenciaComercial\Lc_Documento;

class Lc_DocumentoController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_documento';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Lc_Documento();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function getFilesUrl($params)
    {
        $documento = new Lc_Documento();

        $id_solicitud = $params['id_solicitud'];
        $sql = self::getSqlDocumentos("id_solicitud = $id_solicitud");
        $data = $documento->executeSqlQuery($sql, false);

        foreach ($data as $key => $doc) {
            global $filesUrl;

            $filesUrl = $documento->filesUrl;

            if ($doc['documento']) {
                $codigo = $doc['codigo'];
                $url = $filesUrl . $id_solicitud . "/" . $codigo . '/' .  $doc['documento'];
                $data[$key]['url'] = getBase64String($url, $doc['documento']);
                $data[$key]['loading'] = false;
                $data[$key]['error'] = false;
            } else {
                $data[$key]['url'] = null;
            }
        }
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

        $docType = $_POST['codigo'];
        $file = $_FILES['file'];

        $nameFile = uniqid() . getExtFile($file);
        /* $_POST[$columnFile] = $nameFile; */

        /* Borramos la carpeta del docuemento si existe */
        deleteDir(FILE_PATH . "licencia_comercial/solicitud/$id_solicitud/$docType/");

        /* Generamops la carpeta y obtenemos el path para copiar el archivo */
        $path = getPathFile($file, FILE_PATH . "licencia_comercial/solicitud/$id_solicitud/$docType/", $nameFile);

        /* Capiamos el archivo */
        $copiado = copy($file['tmp_name'], $path);

        $url = null;
        if ($copiado) {
            $documento = new Lc_Documento();
            $tipo_documento = $_POST['tipo_documento'];
            $params = ['id_solicitud' => $id_solicitud, 'id_tipo_documento' => $tipo_documento];
            $idDocumento = $documento->get($params)->value['id'];
            $documento->update(['documento' => $nameFile], $idDocumento);
            $url = $documento->filesUrl . $id_solicitud . '/' . $docType . '/' . $nameFile;
        }

        if ($url) {
            sendRes(['url' => getBase64String($url, $nameFile)]);
        } else {
            sendRes(null, 'Hubo un error a subir un archivo', ['id' => $id_solicitud]);
        };
        exit;
    }

    public function delete($id)
    {
        $data = new Lc_Documento();
        return $data->delete($id);
    }

    public function deleteBySolicitudId($id)
    {
        $sql = "DELETE FROM lc_documentos WHERE id_solicitud = $id AND id_tipo_documento > 10";
        $conn = new BaseDatos();
        return $conn->query($sql);
    }

    public static function getSqlDocumentos($where)
    {
        $sql =
            "SELECT 
                doc.id as tipo_documento,
                ld.documento as documento,
                ld.verificado as verificado,
                doc.nombre as nombre,
                doc.codigo as codigo,
                doc.formato as formato
            FROM dbo.lc_documentos ld
                LEFT JOIN dbo.tipos_documentos doc ON ld.id_tipo_documento = doc.id
            WHERE $where";

        return $sql;
    }

    public static function getDocumentosBySolicitud($id)
    {
        $sql =
            "SELECT 
                doc.id_tipo_documento as value,
                tipo.nombre as label,
                codigo as codigo,
                requiere as req	
            FROM dbo.lc_documentos doc
                LEFT JOIN dbo.tipos_documentos tipo ON doc.id_tipo_documento = tipo.id
            WHERE doc.id_solicitud = $id AND id_tipo_documento > 11";

        $doc = new Lc_Documento();
        return $doc->executeSqlQuery($sql, false);
    }
}
