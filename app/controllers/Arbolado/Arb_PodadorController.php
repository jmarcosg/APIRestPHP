<?php

namespace App\Controllers\Arbolado;

use App\Connections\BaseDatos;
use App\Models\Arbolado\Arb_Podador;
use DateInterval;
use DateTime;
use ErrorException;

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

        $now = new DateTime();

        $req['fecha_revision'] = $now->format('Y-m-d');

        if ($req['estado'] == 'aprobado') {
            /* Establecemos la fecha de vencimiento */
            $interval = new DateInterval('P2Y');
            $now->add($interval);
            $req['fecha_vencimiento'] = $now->format('Y-m-d');

            /* Obtenemos la ultima evalacion del usuario */
            $params = ['id_wappersonas' => $req['id_wappersonas'], 'TOP' => 1];
            $op = ['order' => ' ORDER BY id DESC '];
            $arbEvaluacionController = new Arb_EvaluacionController();
            $evaluacion = $arbEvaluacionController->index($params, $op);
            $idEvalacion = $evaluacion[0]['id'];

            /* Actualizamos la evaluacion con el id de la solicitud */
            $evaluacion = $arbEvaluacionController->update(['id_podador' => $id], $idEvalacion);
        }
        unset($req['id_wappersonas']);

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

    public function getEstadoSolicitudDetalle($id_wappersonas)
    {
        $params = ['id_wappersonas' => $id_wappersonas, 'TOP' => 1];
        $op = ['order' => ' ORDER BY id DESC '];

        $solicitud = $this->index($params, $op);

        if ($solicitud) {
            $solicitud = $solicitud[0];
            $id = $solicitud['id'];
            $venc = $solicitud['fecha_vencimiento'];

            if ($solicitud['estado'] == 'nuevo') {
                return [
                    'estado' => 'nuevo',
                    'msg' => "La solcitud: $id, se encuentra en proceso de revisión",
                ];
            }

            if ($solicitud['estado'] == 'rechazado') {
                $observacion = $solicitud['observacion'];
                return [
                    'estado' => 'rechazado',
                    'msg' => "Solicitud Número: $id fue rechazada. $observacion",
                ];
            }

            if ($solicitud['estado'] == 'aprobado') {
                if (!esVigente($venc)) {
                    $venc = date("d/m/Y", strtotime($venc));
                    return [
                        'estado' => 'vencida',
                        'msg' => "Solicitud Número: $id vencida con fecha $venc",
                    ];
                } else {
                    $venc = date("d/m/Y", strtotime($venc));
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

    public function sendEmail($id, $type, $data)
    {
        $subject = "Sistema Arbolado - Solicitud podador N° $id";

        if ($type == 'envio') $body = $this->templateSolicitudEmail();

        if ($type == 'aprobado') $body = $this->templateSolicitudAprobadaEmail($data);

        if ($type == 'rechazado') $body = $this->templateSolicitudRechazadaEmail($data);

        $response = sendEmail($data['email'], $subject, $body);

        if ($response['error']) {
            $error = new ErrorException($response['error']);
            logFileEE('v1/arbolado', $error, get_class($this), __FUNCTION__);
        }
    }

    /** 
     * Retorna el template de correo electronico para las solicitudes de poda enviadas por el usuario 
     * */
    protected function templateSolicitudEmail()
    {
        $template =
            "<!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8' />
                    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                    <link
                        href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'
                        rel='stylesheet'
                        integrity='sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC'
                        crossorigin='anonymous'
                    />
                </head>
                <body>
                    <div class='container'>
                        <div class='row'>
                            <p>Usted envio una solicitud para ser Podador.</p>
                            <p>La solicitud se encuentra en propeceso de revisión</p>
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    /** 
     * Retorna el template de correo electronico para las solicitudes de poda enviadas por el usuario 
     * */
    protected function templateSolicitudAprobadaEmail($data)
    {
        $observacion = $data['observacion'];

        $template =
            "<!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8' />
                    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                    <link
                        href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'
                        rel='stylesheet'
                        integrity='sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC'
                        crossorigin='anonymous'
                    />
                </head>
                <body>
                    <div class='container'>
                        <div class='row'>
                            <p>La solcitud de podador se encuentra aprobada.</p>
                            <p>Observación: $observacion</p>                            
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    /** 
     * Retorna el template de correo electronico para las solicitudes de poda enviadas por el usuario 
     * */
    protected function templateSolicitudRechazadaEmail($data)
    {
        $observacion = $data['observacion'];

        $template =
            "<!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8' />
                    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                    <link
                        href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'
                        rel='stylesheet'
                        integrity='sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC'
                        crossorigin='anonymous'
                    />
                </head>
                <body>
                    <div class='container'>
                        <div class='row'>
                            <p>La solcitud de podador se encuentra rechazada.</p>
                            <p>Usted puede enviar una nueva solicitud.</p>
                            <p>Observación: $observacion</p>  
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    public function getDatosCarnet($id)
    {
        $sql =
            "SELECT 
                id,
                wap_per.Nombre,
                wap_per.Documento,
                certificado,
                estado,
                fecha_vencimiento,
                fecha_revision,
                genero
            FROM dbo.arb_podadores arb_pod
            LEFT JOIN dbo.wapPersonas wap_per ON arb_pod.id_wappersonas  = wap_per.ReferenciaID
            WHERE id = $id";

        $conn = new BaseDatos();
        $query =  $conn->query($sql);

        return odbc_fetch_array($query);
    }
}
