<?php

namespace App\Traits\Arbolado;

use ErrorException;

trait TemplateEmail
{
    public function sendEmail($id, $type, $data)
    {
        $subject = "Sistema Arbolado - Solicitud podador N° $id";

        if ($type == 'envio') $body = $this->templateSolicitudEmail();

        if ($type == 'aprobado') $body = $this->templateSolicitudAprobadaEmail($data);

        if ($type == 'rechazado') $body = $this->templateSolicitudRechazadaEmail($data);

        if ($type == 'deshabilitado') $body = $this->templateSolicitudDeshabilitadoEmail($data);

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

    /** 
     * Retorna el template de correo electronico para las solicitudes de poda enviadas por el usuario 
     * */
    protected function templateSolicitudDeshabilitadoEmail($data)
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
                            <p>Usted fue deshabilitado como podador</p>
                            <p>Observación: $observacion</p>  
                        </div>
                    </div>
                </body>
            </html>";
        return $template;
    }
}
