<?php

namespace App\Models\LicenciaComercial;

use App\Connections\BaseDatos;
use App\Models\BaseModel;
use ErrorException;

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

    public function saveInitDocuments($idSolicitud)
    {
        $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 1, 'verificado' => 0];
        $this->set($params);
        $this->save();
    }
}
