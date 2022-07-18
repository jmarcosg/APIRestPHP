<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Audit;
use App\Models\Arbolado\Arb_Evaluacion;
use App\Controllers\Arbolado\Arb_PodadorController;
use ErrorException;

class Arb_EvaluacionController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_evaluacion';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new Arb_Evaluacion();
        $data = $data->list($param, $ops)->value;
        sendRes($data);
        exit;
    }

    public static function indexData($param = [], $ops = [])
    {
        $data = new Arb_Evaluacion();
        return $data->list($param, $ops)->value;
    }

    public static function getEvaluacionMsg()
    {
        $params = ['id_wappersonas' => $_GET['id_wappersonas'], 'TOP' => 1, 'id_podador' => null];
        $op = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Evaluacion();
        $data = $data->list($params, $op)->value;

        if (count($data) > 0) {
            $data = $data[0];
        } else {
            $data = ['msg' => 'No presenta evaluación'];
        }
        sendRes($data);
        exit;
    }

    public static function getPresetEvaluacion()
    {
        $params = ['id_wappersonas' => $_GET['id_wappersonas'], 'TOP' => 1, 'id_podador' => null];
        $op = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Evaluacion();
        $data = $data->list($params, $op)->value;

        if (count($data) > 0) {
            $data = ['msg' => 'Ya presenta una evaluación cargada'];
        } else {
            $data = ['msg' => null];
        }
        sendRes($data);
        exit;
    }

    public static function get($params)
    {
        $data = new Arb_Evaluacion();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function saveEvaluacion()
    {
        /* Configuracion de los parametros */
        $idWappersona = $_POST['id_wappersonas'];
        $params = ['id_wappersonas' => $idWappersona, 'TOP' => 1, 'id_podador' => null];
        $op = ['order' => ' ORDER BY id DESC '];

        /* buscamos la evaluacion */
        $data = new Arb_Evaluacion();
        $evaluacion = $data->list($params, $op)->value;

        /* Si no encontramos una evaluacion, lo guardamos */
        if (!count($evaluacion) > 0) {
            $id = self::store($_POST);
            if (!$id instanceof ErrorException) {
                sendRes(['id' => $id]);
            } else {
                sendRes(null, $id->getMessage(), $_GET);
            }
            exit;
        }

        /* Buscamos la solicitu del podador */
        $solicitudPodador = Arb_PodadorController::indexData("id_wappersonas = $idWappersona");

        /* Si existe una solicitud, analizamos situacion */
        if (count($solicitudPodador) > 0) {
            $solicitudPodador = $solicitudPodador[0];
            $fechaVenc = $solicitudPodador['fecha_vencimiento'];

            /* Si no esta vigente, guardamos la evaluacion */
            if (!esVigente($fechaVenc)) {
                $id = self::store($_POST);
                if (!$id instanceof ErrorException) {
                    sendRes(['id' => $id]);
                } else {
                    sendRes(null, $id->getMessage(), $_GET);
                }
                exit;
            } else {
                $response = [
                    'msg' =>  'Presenta una solicitud vigente',
                    'solicitudPodador' => $solicitudPodador
                ];
                sendRes($response);
                exit;
            }
        } else {
            $id = self::store($_POST);
            if (!$id instanceof ErrorException) {
                sendRes(['id' => $id]);
            } else {
                sendRes(null, $id->getMessage(), $_GET);
            }
            exit;
        }
    }

    public static function store($res)
    {
        $data = new Arb_Evaluacion();
        $data->set($res);
        $id = $data->save();

        /* Generamos registro para la auditoria */
        $audit = new Arb_Audit();
        $audit->set([
            'id_usuario' => $res['id_usuario_admin'],
            'id_wappersonas' => $res['id_wappersonas_admin'],
            'id_evaluacion' => $id,
            'accion' => 'agrego',
        ]);
        $audit->save();

        return $id;
    }

    public static function update($req, $id)
    {
        $data = new Arb_Evaluacion();
        return $data->update($req, $id);
    }

    public static function delete($id)
    {
        $data = new Arb_Evaluacion();
        return $data->delete($id);
    }
}
