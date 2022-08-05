<?php

namespace App\Traits\Arbolado;

use ErrorException;
use App\Controllers\Arbolado\Arb_SolicitudController;

trait TemplateEmailSolicitud
{
    public static function  sendEmail($id, $type, $data)
    {
        $subject = "Sistema Arbolado - Solicitud N° $id";

        $attachments = null;
        if ($type == 'envio') $body = self::templateSolicitudEmail($data);

        if ($type == 'aprobado') {
            $solicitudController =  new Arb_SolicitudController();
            $fileName = $id . '_' . date('Ymd') . '.pdf';
            $solicitudController->getSolicitudPodaPdf($id, $fileName);
            $attachments = [$fileName];
            $body = self::templateSolicitudAprobadaEmail($data);
        }

        if ($type == 'rechazado') $body = self::templateSolicitudRechazadaEmail($data);

        $response = sendEmail($data['email'], $subject, $body, $attachments);

        if ($response['error']) {
            $error = new ErrorException($response['error']);
            logFileEE('v1/arbolado', $error, get_class(), __FUNCTION__);
        }
    }

    /** 
     * Retorna el template de correo electronico para las solicitudes de poda enviadas por el usuario 
     * */
    protected static function templateSolicitudEmail($data)
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
    protected static function templateSolicitudAprobadaEmail($data)
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
                            <a href='#'>Listado de podadores habilitados</a>
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }
    /** 
     * Retorna el template de correo electronico para las solicitudes de poda rechazadas
     * */
    protected static function templateSolicitudRechazadaEmail($data)
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
                            <h3>La solicitud Número: $id fue rechazada. Aguarde la visita de un inspector en su domicilio </h3>
                            <br />
                            <p>Observación: $observacion</p>
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }
}
