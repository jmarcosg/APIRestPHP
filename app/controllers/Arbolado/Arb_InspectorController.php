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
        return $data;
    }

    public static function store($res)
    {
        $exist = self::get(['dni' => $res['dni']]);

        if (!$exist) {
            /* Guardamos el inspector */
            $data = new Arb_Inspector();
            $data->set($res);
            $id = $data->save();

            if (!$id instanceof ErrorException) {
                /* Generamos registro para la auditoria */
                $audit = new Arb_Audit();
                $audit->set([
                    'id_usuario' => $res['id_usuario_admin'],
                    'id_wappersonas' => $res['id_wappersonas_admin'],
                    'id_inspector' => $id,
                    'accion' => 'agrego',
                ]);
                $audit->save();

                sendRes(['id' => $id]);
            } else {
                sendRes(null, $id->getMessage(), $_GET);
            };
        } else {
            sendRes(null, 'Ya se encuentra registrado', ['dni' => $res['dni']]);
        }
        exit;
    }

    public function update($req, $id)
    {
        $data = new Arb_Inspector();
        return $data->update($req, $id);
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
