<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Audit;
use App\Models\Arbolado\Arb_Inspector;
use ErrorException;

class Arb_InspectorController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_evaluacion';
    }

    public static function index()
    {
        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Inspector();

        $data = $data->list($_GET, $ops)->value;

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public static function get($params)
    {
        $data = new Arb_Inspector();
        $data = $data->get($params)->value;

        if (!$data instanceof ErrorException) {
            if ($data !== false) {
                sendRes($data);
            } else {
                sendRes(null, 'No se encontro la evaluación', $_GET);
            }
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };
        exit;
    }

    public static function store()
    {
        $data = new Arb_Inspector();
        $exist = $data->get(['dni' => $_POST['dni']])->value;

        if (!$exist) {
            /* Guardamos el data */
            $data->set($_POST);
            $id = $data->save();

            if (!$id instanceof ErrorException) {
                /* Generamos registro para la auditoria */
                $audit = new Arb_Audit();
                $audit->set([
                    'id_usuario' => $_POST['id_usuario_admin'],
                    'id_wappersonas' => $_POST['id_wappersonas_admin'],
                    'id_inspector' => $id,
                    'accion' => 'agrego',
                ]);
                $audit->save();

                sendRes(['id' => $id]);
            } else {
                sendRes(null, $id->getMessage(), $_GET);
            };
        } else {
            sendRes(null, 'Ya se encuentra registrado', ['dni' => $_POST['dni']]);
        }
        exit;
    }

    public static function update($id)
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        $data = new Arb_Inspector();
        $data = $data->update($_PUT, $id);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function delete($id)
    {
        $data = new Arb_Inspector();
        $inspector = $data->delete($id);

        if (!$inspector instanceof ErrorException) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, $inspector->getMessage(), ['id' => $id]);
        };
        exit;
    }
}
