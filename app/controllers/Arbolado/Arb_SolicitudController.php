<?php

namespace App\Controllers\Arbolado;

use App\Models\Arbolado\Arb_Solicitud;
use ErrorException;

class Arb_SolicitudController
{
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
        $data = new Arb_Solicitud();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Solicitud();
        return $data->delete($id);
    }

    public function sendEmailSolicitud($id)
    {
        $email = $_POST['email'];
        $subject = "Sistema Arbolado - Solicitud N° $id";
        $body = $this->templateEmail();

        $response = sendEmail($email, $subject, $body);

        if ($response['error']) {
            $error = new ErrorException($response['error']);
            logFileEE('v1/arbolado', $error, get_class($this), __FUNCTION__);
        }
    }

    protected function templateEmail()
    {
        $tipo = $_POST['tipo'];
        $solicita = $_POST['solicita'];
        $ubicacion = $_POST['ubicacion'];
        $motivo = $_POST['motivo'];
        $cantidad = $_POST['cantidad'];

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
}
