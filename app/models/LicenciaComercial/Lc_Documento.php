<?php

namespace App\Models\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\BaseModel;

class Lc_Documento extends BaseModel
{
    protected $table = 'lc_documentos';
    protected $logPath = 'v1/licencia_comercial';
    protected $identity = 'id';

    protected $fillable = [
        'id_solicitud',
        'id_tipo_documento',
        'documento',
        'verificado',
    ];

    public $filesUrl = FILE_PATH . 'licencia_comercial/solicitud/';

    public function saveInitDocuments($idSolicitud, $solicitud)
    {
        /* Impuesto Municipal */
        $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 1, 'verificado' => 0];
        $this->set($params);
        $this->save();

        $params['id_tipo_documento'] = 10;
        $this->set($params);
        $this->save();

        $params['id_tipo_documento'] = 4;
        $this->set($params);
        $this->save();

        $params['id_tipo_documento'] = 6;
        $this->set($params);
        $this->save();

        if ($solicitud['pertenece'] == 'tercero') {
            $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 2, 'verificado' => 0];
            $this->set($params);
            $this->save();

            $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 3, 'verificado' => 0];
            $this->set($params);
            $this->save();
        }

        if ($solicitud['tipo_persona'] == 'fisica') {
            $params['id_tipo_documento'] = 5;
            $this->set($params);
            $this->save();

            $params['id_tipo_documento'] = 7;
            $this->set($params);
            $this->save();
        }

        if ($solicitud['tipo_persona'] == 'juridica') {
            $params['id_tipo_documento'] = 8;
            $this->set($params);
            $this->save();

            /* AFIP */
            $params['id_tipo_documento'] = 9;
            $this->set($params);
            $this->save();
        }
    }

    public function deleteInitDocuments($idSolicitud)
    {
        $documentos = $this->list(['id_solicitud' => $idSolicitud])->value;

        foreach ($documentos as $doc) {
            $this->delete($doc['id']);
        }
    }

    public static function documentosUpdate($req, $id)
    {
        $documentos = explode(",", $req['documentos']);
        unset($req['documentos']);

        /* Borramos los documentos con id mayor a 10 */
        self::deleteBySolicitudId($id);

        /* Actualizamos los nuevos documentos */
        $documento = new Lc_Documento();
        foreach ($documentos as $d) {
            $documento->set(['id_solicitud' => $id, 'id_tipo_documento' => $d, 'verificado' => 0]);
            $documento->save();
        }
    }

    public static function deleteBySolicitudId($id)
    {
        /* Los primeros 10 documentos son fijos segun corresponde, persona fisica o persona jurifica */
        $sql = "DELETE FROM lc_documentos WHERE id_solicitud = $id AND id_tipo_documento >= 11";
        $conn = new BaseDatos();
        return $conn->query($sql);
    }

    public static function getNota($data, $nota)
    {
        $id = $data['id'];
        if ($data[$nota]) {
            $documento = new Lc_Documento();
            $filesUrl = $documento->filesUrl;
            $url = $filesUrl . $id . "/" . $nota  . '/' .  $data[$nota];
            return [
                'url' => getBase64String($url, $data[$nota]),
                'loading' =>  false,
                'error' => false
            ];
        }
        return null;
    }
}
