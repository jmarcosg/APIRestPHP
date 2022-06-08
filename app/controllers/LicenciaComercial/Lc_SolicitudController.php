<?php

namespace App\Controllers\LicenciaComercial;

use App\Models\LicenciaComercial\Lc_Solicitud;
use App\Controllers\RenaperController;

use ErrorException;

class Lc_SolicitudController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_solicitud';
    }
    public function index($param = [], $ops = [])
    {
        $data = new Lc_Solicitud();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new Lc_Solicitud();
        $data = $data->get($params)->value;

        /* Si la solicitud tiene cargado un tercero, lo buscamos por renaper */
        if ($data['pertenece'] == 'tercero') {
            $rc = new RenaperController();
            $dni = $data["dni_tercero"];
            $tramite = $data["tramite_tercero"];
            $genero = $data["genero_tercero"];
            $data['dataTercero'] = $rc->getDataTramite($genero, $dni, $tramite);
        }

        return $data;
    }

    public function store($res)
    {
        $res['estado'] = 'act';
        $data = new Lc_Solicitud();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Lc_Solicitud();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Lc_Solicitud();
        return $data->delete($id);
    }

    public function sendEmail($id, $type, $data)
    {
        $subject = "Sistema Arbolado - Solicitud N° $id";

        if ($type == 'envio') $body = $this->templateSolicitudEmail($data);

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
    protected function templateSolicitudEmail($data)
    {
        $tipo = $data['tipo'];
        $solicita = $data['solicita'];
        $ubicacion = $data['ubicacion'];
        $motivo = $data['motivo'];
        $cantidad = $data['cantidad'];

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
                            <h3>Detalle de la solicitud enviada</h3>
                            <hr />
                            <p>Tipo: $tipo</p>
                            <p>Solicita: $solicita</p>
                            <p>Ubicación: $ubicacion</p>
                            <p>Motivo: $motivo</p>
                            <p>Cantidad: $cantidad</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    /** 
     * Retorna el template de correo electronico para las solicitudes de poda aprobadas
     * */
    protected function templateSolicitudAprobadaEmail($data)
    {
        $id = $data['id'];
        $observacion = $data['observacion'];
        $contacto = $data['contacto'];

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
                            <h3>La solicitud Número: $id ya se encuentra aprobada</h3>
                            <br />
                            <p>Observación: $observacion</p>
                            <hr />
                            <p>El personal se va contactar al número: $contacto</p>
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }
    /** 
     * Retorna el template de correo electronico para las solicitudes de poda rechazadas
     * */
    protected function templateSolicitudRechazadaEmail($data)
    {
        $id = $data['id'];
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
                            <h3>La solicitud Número: $id fue rechazada. Puede generar una nueva solicitud </h3>
                            <br />
                            <p>Observación: $observacion</p>
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }
}
