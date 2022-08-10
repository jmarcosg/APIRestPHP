<?php

namespace App\Traits\LicenciaComercial;

use ErrorException;

trait TemplateEmailSolicitud
{
    public static function  sendEmail($id, $type, $data = [])
    {
        $subject = "Licencia Comercial - Solicitud N° $id";

        /* Inicio tramite */
        if ($type == 'inicio') $body = self::init($data);

        /* Modulo verificador de rubros */
        if ($type == 'rubros_aprobado') $body = self::verRubrosAprobado($data);
        if ($type == 'rubros_rechazado') $body = self::verRubrosRechazado($data);
        if ($type == 'rubros_retornado') $body = self::verRubrosRetornado($data);

        /* Envio a modulo verificador de documentos */
        if ($type == 'documentacion') $body = self::sendDocumentacion($data);

        /* Modulo verificador de documentos */
        if ($type == 'documentos_aprobado') $body = self::verDocumentosAprobado($data);
        if ($type == 'documentos_rechazado') $body = self::verDocumentosRechazado($data);
        if ($type == 'documentos_retornado') $body = self::verDocumentosRetornado($data);

        $attachments = null;

        if ($type == 'rechazado') $body = self::templateSolicitudRechazadaEmail($data);

        $response = sendEmail($data['correo'], $subject, $body, $attachments);

        if ($response['error']) {
            $error = new ErrorException($response['error']);
            logFileEE('v1/arbolado', $error, get_class(), __FUNCTION__);
        }
    }

    /* Inicio tramite */
    protected static function init($data)
    {
        $id = $data['id'];

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
                            <h3>Usted inicio un tramite de licencia comercial: $id</h3>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    /* Verificador de rubros */
    protected static function verRubrosAprobado($data)
    {

        $id = $data['id'];
        $obs = $data['observacion'];

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
                            <h3>Verficador de rubros aprobo la solicitud: $id</h3>
                            <hr />
                            <p>Observacion: $obs</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    protected static function verRubrosRechazado($data)
    {

        $id = $data['id'];
        $obs = $data['observacion'];

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
                            <h3>Verficador de rubros rechazo la solicitud: $id</h3>
                            <hr />
                            <p>Observacion: $obs</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    protected static function verRubrosRetornado($data)
    {

        $id = $data['id'];
        $obs = $data['observacion'];

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
                            <h3>Verficador de rubros retorno la solicitud: $id</h3>
                            <hr />
                            <p>Observacion: $obs</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    /* Envio de documentacion a verificador de documentos */
    protected static function sendDocumentacion($data)
    {
        $id = $data['id'];
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
                            <h3>Usted envio la documentacion de la solicitud: $id</h3>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }


    /* Verificador de rubros */
    protected static function verDocumentosAprobado($data)
    {

        $id = $data['id'];
        $obs = $data['observacion'];

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
                            <h3>Verficador de documentos aprobo la solicitud: $id</h3>
                            <hr />
                            <p>Observacion: $obs</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    protected static function verDocumentosRechazado($data)
    {

        $id = $data['id'];
        $obs = $data['observacion'];

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
                            <h3>Verficador de documentos rechazo la solicitud: $id</h3>
                            <hr />
                            <p>Observacion: $obs</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }

    protected static function verDocumentosRetornado($data)
    {

        $id = $data['id'];
        $obs = $data['observacion'];

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
                            <h3>Verficador de documentos retorno la solicitud: $id</h3>
                            <hr />
                            <p>Observacion: $obs</p>
                            <hr />
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }
}
