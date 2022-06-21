<?php

namespace App\Models\Arbolado;

use App\Models\BaseModel;

use App\Models\WapPersona;
use App\Controllers\Arbolado\Arb_EvaluacionController;

class Arb_Podador extends BaseModel
{
    protected $table = 'arb_podadores';
    protected $logPath = 'v1/arbolado';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = [
        'id_usuario',
        'id_wappersonas',
        'certificado',
        'capacitador',
        'observacion',
        'estado',
        'fecha_vencimiento',
        'fecha_revision',
        'id_usuario_admin',
        'id_wappersonas_admin',
        'fecha_deshabilitado',
        'motivo_deshabilitado'
    ];

    protected $filesUrl = FILE_PATH . 'Arbolado/podador/';

    function wapPersona()
    {
        return $this->hasOne(WapPersona::class, 'id_wappersonas',  'ReferenciaID');
    }

    function wapPersonaAdmin()
    {
        return $this->hasOne(WapPersona::class, 'id_wappersonas_admin',  'ReferenciaID');
    }

    function evaluacion()
    {
        $arbEvaluacionController = new Arb_EvaluacionController();

        if (isset($this->value[0]) && is_array($this->value[0])) {
            foreach ($this->value as $key => $elem) {
                $params = ['id_wappersonas' => $elem['id_wappersonas'], 'TOP' => 1, 'id_podador' => null];
                if ($elem['estado'] == 'nuevo') {
                    $params['id_podador'] = null;
                } else {
                    $params['id_podador'] = $elem['id'];
                }
                $op = ['order' => ' ORDER BY id DESC '];

                $evaluacion = $arbEvaluacionController->index($params, $op);

                if (count($evaluacion) > 0) {
                    $evaluacion = $evaluacion[0];
                } else {
                    $evaluacion = null;
                }

                $this->value[$key]['evaluacion'] = $evaluacion;
            }
        } else {
            $elem = $this->value;

            if (count($elem) > 0) {
                $params = ['id_wappersonas' => $elem['id_wappersonas'], 'TOP' => 1, 'id_podador' => null];
                if ($elem['estado'] == 'nuevo') {
                    $params['id_podador'] = null;
                } else {
                    $params['id_podador'] = $elem['id'];
                }
                $op = ['order' => ' ORDER BY id DESC '];

                $evaluacion = $arbEvaluacionController->index($params, $op);

                if (count($evaluacion) > 0) {
                    $evaluacion = $evaluacion[0];
                } else {
                    $evaluacion = null;
                }
                $this->value['evaluacion'] = $evaluacion;
            }
        }
    }

    function certificado()
    {
        if (isset($this->value['certificado'])) {
            $name = $this->value['certificado'];
            $this->value['certificado'] = [
                'name' => $name,
                'path' => $this->filesUrl . $this->value['id'] . '/' . $name,
            ];
        }
    }
}
