<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Podador;

class Arb_PodadorController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_Podador';
    }
    public function index($param = [], $ops = [])
    {
        $data = new Arb_Podador();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Podador();
        $data = $data->get($params)->value;
        return $data;
    }

    public function store($res)
    {
        $res['estado'] = 'nuevo';
        $data = new Arb_Podador();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Podador();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Podador();
        return $data->delete($id);
    }

    public function existeSol($id_usuario)
    {
        $params = ['id_usuario' => $id_usuario, 'estado' => 'nuevo', 'TOP' => 1];
        $op = ['order' => ' ORDER BY id DESC '];

        $solicitud = $this->index($params, $op);

        if ($solicitud) {
            return $solicitud[0];
        }

        return $solicitud;
    }

    public function getEstadoSolicitudDetalle($id_usuario)
    {
        $params = ['id_usuario' => $id_usuario, 'TOP' => 1];
        $op = ['order' => ' ORDER BY id DESC '];

        $solicitud = $this->index($params, $op);

        if ($solicitud) {
            $solicitud = $solicitud[0];
            $id = $solicitud['id'];
            $venc = $solicitud['fecha_vencimiento'];

            if ($solicitud['estado'] == 'nuevo') {
                return [
                    'estado' => 'nuevo',
                    'msg' => "Usted ya envio una solicitud con número: $id, se encuentra en proceso de evaluación",
                ];
            }

            if ($solicitud['estado'] == 'rechazado') {
                return [
                    'estado' => 'rechazado',
                    'msg' => "Solicitud Número: $id fue rechazada",
                ];
            }

            if ($solicitud['estado'] == 'aprobado') {
                $arrayFechas = compararFechas($venc, 'days', 'Y-m-d');

                if ($arrayFechas['date'] <= $arrayFechas['now']) {
                    return [
                        'estado' => 'vencida',
                        'msg' => "Solicitud Número: $id vencida con fecha $venc",
                    ];
                } else {
                    return [
                        'estado' => 'vigente',
                        'msg' => "El carnet se encuentra vigente hasta la fecha: $venc",
                    ];
                }
            }
        }

        return [
            'estado' => null,
            'msg' => null,
        ];;
    }
}
