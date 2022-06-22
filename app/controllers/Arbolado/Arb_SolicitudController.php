<?php

namespace App\Controllers\Arbolado;

use App\Traits\Arbolado\TemplateEmailSolicitud;

use App\Models\Arbolado\Arb_Solicitud;
use App\Models\Arbolado\Arb_Audit;

class Arb_SolicitudController
{
    use TemplateEmailSolicitud;

    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_solicitud';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Arb_Solicitud();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Solicitud();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $res['estado'] = 'nuevo';
        $data = new Arb_Solicitud();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        /* Generamos registro para la auditoria */
        $audit = new Arb_Audit();
        $audit->set([
            'id_usuario' => $req['id_usuario'],
            'id_wappersonas' => $req['id_wappersonas'],
            'id_solicitud' => $id,
            'accion' => $req['estado'],
        ]);
        $audit->save();

        /* Modificamos el registro */
        $data = new Arb_Solicitud();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Solicitud();
        return $data->delete($id);
    }
}
