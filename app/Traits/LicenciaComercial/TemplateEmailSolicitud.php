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

        /* Modulo verificador inicial */
        if ($type == 'inicio_aprobado') $body = self::verInicioAprobado($data);
        if ($type == 'inicio_rechazado') $body = self::verInicioRechazado($data);
        if ($type == 'inicio_retornado') $body = self::verInicioRetornado($data);

        /* Modulo verificador ambiental y catastro */
        if ($type == 'catastro_aprobado') $body = self::verCatAprobado($data);
        if ($type == 'catastro_rechazado') $body = self::verCatRechazado($data);
        if ($type == 'catastro_retornado') $body = self::verCatRetornado($data);

        /* Modulo verificador ambiental */
        if ($type == 'ambiental_aprobado') $body = self::verAmbAprobado($data);
        if ($type == 'ambiental_rechazado') $body = self::verAmbRechazado($data);

        /* Modulo verificador de rubros */
        if ($type == 'rubros_aprobado') $body = self::verRubrosAprobado($data);
        if ($type == 'rubros_rechazado') $body = self::verRubrosRechazado($data);

        /* Envio a modulo verificador de documentos */
        if ($type == 'documentacion') $body = self::sendDocumentacion($data);

        /* Modulo verificador de documentos */
        if ($type == 'documentos_aprobado') $body = self::verDocumentosAprobado($data);
        if ($type == 'documentos_rechazado') $body = self::verDocumentosRechazado($data);
        if ($type == 'documentos_retornado') $body = self::verDocumentosRetornado($data);

        $attachments = null;

        if ($type == 'rechazado') $body = self::templateSolicitudRechazadaEmail($data);

        if (PROD) {
            $response = sendEmail($data['correo'], $subject, $body, $attachments);
            if ($response['error']) {
                $error = new ErrorException($response['error']);
                logFileEE('v1/arbolado', $error, get_class(), __FUNCTION__);
            }
        }
    }

    /* Inicio tramite */
    protected static function init($data)
    {
        $id = $data['id'];

        $template =
            "<div class='row'>
                <p>Sr. Contribuyente usted inicio un trámite de Licencia Comercial.</p>
                <p>El presente inicio de trámite no habilita el desarrollo de la actividad comercial, 
                la misma quedara sujeta a informe del área de Inspecciones.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /* Verificador incial */
    protected static function verInicioAprobado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la documentación fue ingresada 
                correctamente y pasará al sector de verificación de domicilio.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verInicioRechazado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p> Sr. contribuyente se le informa que la solicitud presentada fue rechazada. Inicie nuevamente el 
                trámite con lo solicitado en el sistema o comuníquese telefónicamente o WhatsApp al 2993240143</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verInicioRetornado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que su solicitud se encuentra observada debido a un 
                problema en la carga de datos/documentación.</p>
                <p>Verifique la misma y envíela nuevamente. Por consultas  telefónicamente o WhatsApp al 2993240143.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /* Catastro */
    protected static function verCatAprobado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la documentación fue ingresada 
                correctamente y pasará al sector de verificación de ambiental.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verCatRechazado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la solicitud presentada fue rechazada. 
                Inicie nuevamente el trámite con lo solicitado en el sistema o comuníquese telefónicamente al 0299-4491200 
                Int 4342 o a través de situn@muninqn.gov.ar</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verCatRetornado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que su solicitud se encuentra observada debido a 
                un problema en la carga de datos/documentación.</p>
                <p>Verifique la misma y envíela nuevamente.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /* Verificacion ambiental */
    protected static function verAmbAprobado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr contribuyente se le informa que la factibilidad de zona para explotar el rubro solicitado es correcto.</p> 
                <p>Su trámite pasara al sector de auditoría</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verAmbRechazado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la solicitud presentada fue rechazada. 
                Comuníquese telefónicamente al 0299-4491200 Int 4361 o a
                través de certificacinesambientales@muninqn.gov.ar</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /* Verificador de rubros */
    protected static function verRubrosAprobado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que el rubro solicitado fue debidamente codificado. </p>
                <p>Verifique en el sistema si se le requiere adjuntar documentación específica por su actividad, 
                para poder dar curso al trámite. De no ser requerida otra documentación su trámite continuará normalmente.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verRubrosRechazado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la solicitud presentada fue rechazada. 
                Comuníquese telefónicamente o WhatsApp al 2993240143</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /* Envio de documentacion a verificador de documentos */
    protected static function sendDocumentacion($data)
    {
        $id = $data['id'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la documentación fue ingresada 
                correctamente y pasará al sector de verificación.</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /* Verificador de rubros */
    protected static function verDocumentosAprobado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que su solicitud fue aprobada. </p>
                <p>Ya puede visualizar el resumen de su trámite.</p>
                <p>Dentro de los próximos 5 días hábiles se contactarán telefónicamente desde el área de inspecciones  para realizar la Habilitación de su actividad.</p>
                <p>Se solicita que al momento de la inspección se encuentre el Titular o Apoderado a efectos de firmar el Acta correspondiente. </p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verDocumentosRechazado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que la solicitud presentada fue rechazada. Comuníquese telefónicamente o WhatsApp al 2993240143</p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    protected static function verDocumentosRetornado($data)
    {
        $id = $data['id'];
        $obs = $data['observacion'];

        $template =
            "<div class='row'>
                <p>Sr. contribuyente se le informa que su solicitud se encuentra observada debido a un problema en la carga de datos/documentación.</p>
                <p>Verifique la misma y envíela nuevamente. </p>
            </div>";

        $template = self::getTemplateInLayout($template);

        return $template;
    }

    /** Layout principal */
    public static function getTemplateInLayout($template)
    {
        return "<!DOCTYPE html>
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
                        $template
                    </div>
                    <p>Muchas gracias.</p>
                    <p>SUBSECRETARÍA DE COMERCIO.</p>
                </body>
            </html>";
    }
}
