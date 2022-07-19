<?php

namespace App\Controllers\Arbolado;

use App\Traits\Arbolado\TemplateEmailSolicitud;
use App\Traits\Arbolado\SolicitudPodaSql;

use App\Models\Arbolado\Arb_Solicitud;
use App\Models\Arbolado\Arb_Audit;

use App\Models\Arbolado\MYPDF;
use ErrorException;

class Arb_SolicitudController
{
    use TemplateEmailSolicitud, SolicitudPodaSql;

    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_solicitud';
    }

    public static function index($where = "1=1")
    {
        $solicitud = new Arb_Solicitud();

        $sql = self::getSql($where);
        $data = $solicitud->executeSqlQuery($sql, false);
        $data = self::formatDataArray($data);

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public static function getById($id)
    {
        $solicitud = new Arb_Solicitud();

        $sql = self::getSql("sol.id = $id");
        $data = $solicitud->executeSqlQuery($sql, true);
        $data = self::formatData($data);
        $data['archivos'] = $solicitud->archivos($id);

        if (!$data instanceof ErrorException) {
            if ($data !== false) {
                sendRes($data);
            } else {
                sendRes(null, 'No se encontro la solicitud', $_GET);
            }
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };
        exit;
    }

    public static function get($params)
    {
        $data = new Arb_Solicitud();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function store($res)
    {
        $res['estado'] = 'nuevo';
        $data = new Arb_Solicitud();
        $data->set($res);

        $id = $data->save();

        if (!$id instanceof ErrorException) {
            $arbArchivoController = new Arb_ArchivoController();

            foreach ($_FILES as $key => $file) {
                /* Generamos un nombre unico para el archivo */
                $nameFile = uniqid() . getExtFile($file);

                /* Guardamos el nombre del archivo en la tabla */
                $req = ['id_solicitud' => $id, 'name' => $nameFile];
                $archivo = $arbArchivoController->store($req);

                /* copiamos el archivo en la carpeta correspondiente */
                $path = getPathFile($file, FILE_PATH_LOCAL . "arbolado/solicitud_poda/$id/", $nameFile);
                $copiado = copy($file['tmp_name'], $path);

                if ($archivo instanceof ErrorException || !$copiado) {
                    /* Si hubo un error en algun archivo */
                    self::delete($id);
                    sendRes(null, $archivo, $_GET);
                    exit;
                }
            }

            /* Enviamos el correo electronico */
            $data  = [
                'email' => $_POST['email'],
                'tipo' => $_POST['tipo'],
                'solicita' => $_POST['solicita'],
                'ubicacion' => $_POST['ubicacion'],
                'motivo' => $_POST['motivo'],
                'cantidad' => $_POST['cantidad']
            ];
            self::sendEmail($id, 'envio', $data);

            sendRes(['id' => $id]);
        } else {
            sendRes(null, $id->getMessage(), $_GET);
        };
        exit;
    }

    public static function update($req, $id)
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

        /* Extraemos el contacto y el email  */
        $contacto = $req['contacto'];
        $email = $req['email'];
        unset($req['contacto']);
        unset($req['email']);

        /* Modificamos el registro */
        $data = new Arb_Solicitud();
        if ($req['estado'] == 'rechazado') {
            unset($req["cantidad_autorizado"]);
            unset($req["cantidad_reponer"]);
            unset($req["dias_reponer"]);
            unset($req["especie"]);
            unset($req["constancia_danio"]);
        }
        $arbolado = $data->update($req, $id);

        if (!$arbolado instanceof ErrorException) {
            /* Enviamos el correo electronico */
            $data = [
                'id' => $id,
                'email' => $email,
                'contacto' => $contacto,
                'observacion' => $req['observacion']
            ];

            self::sendEmail($id, $req['estado'], $data);
            $req['id'] = $id;
            sendRes($req);
        } else {
            sendRes(null, $arbolado->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public function getSolicitudPodaPdf($id, $fileName)
    {
        $solicitud = new Arb_Solicitud();

        $sql = self::getSql("sol.id = $id");
        $data = $solicitud->executeSqlQuery($sql, true);
        $data = self::formatData($data);

        $data = utf8ize($data);
        $pdf = new MYPDF('P', 'mm');
        $font = 'helvetica';

        // set document information
        $pdf->SetCreator('Municipalidad de Neuquén');
        $pdf->SetAuthor('Municipalidad de Neuquén');
        $pdf->SetTitle('Acta de solicitud - ' . date('d/m/Y'));
        $pdf->SetSubject('Acta de solicitud');
        $pdf->SetKeywords('Acta de solicitud');

        // add a page
        $pdf->AddPage();

        $pdf->SetFont($font, 'B', 15);
        $pdf->Text(15, 30, 'ACTA DE SOLICITUD');
        $pdf->SetFont($font, '', 11);
        $pdf->Text(15, 37, 'N°');
        $pdf->Text(21, 37, $id);
        $pdf->Text(15, 43, 'Neuquén ' . date('d/m/Y'));
        $pdf->Text(15, 49, 'Estado');
        $pdf->SetFont($font, 'b', 12);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Text(30, 49, 'Aprobado');
        $pdf->SetTextColor(0, 0, 0);

        if (ENV == 'replica' || ENV == 'prod') {
            $bannerUrl = 'C:\webApps\Produccion\webLogin\apps\estilos_globales\logo-credencial.png';
        } else {
            $bannerUrl = 'https://weblogin.muninqn.gov.ar/apps/estilos_globales/logo-credencial.png';
        }

        $pdf->Image($bannerUrl, 100, 13, 97.3, 14, 'PNG');

        $pdf->SetFont($font, '', 8);
        $pdf->Text(123, 30, 'SECRETARIA DE SERVICIOS URBANOS');
        $pdf->Text(108, 34, 'SUBSECREATARIA DE LIMPIEZA URBANA Y ESPACIOS VERDES');
        $pdf->Text(118, 38, 'DIRECCION MUNICIPAL DE ESPACIOS VERDES');
        $pdf->Text(125, 42, 'DIRECCION DE ARBOLADO URBANO');
        $pdf->Text(118, 46, 'DIVISION INSPECCIONES Y ARBOLADO URBANO');

        $rowTitle = 60;
        function getrowTitleValTitle(&$rowTitle, $isTitle = false)
        {
            if ($isTitle) {
                $rowTitle = $rowTitle  + 10;
                return $rowTitle;
            } else {
                $rowTitle = $rowTitle  + 5;
                return $rowTitle;
            }
        }

        $rowDesc = 60;
        function getrowTitleValDesc(&$rowDesc, $isTitle = false)
        {
            if ($isTitle) {
                $rowDesc = $rowDesc  + 10;
                return $rowDesc;
            } else {
                $rowDesc = $rowDesc  + 5;
                return $rowDesc;
            }
        }

        $fontSizeTitle = 11;
        $fontSizeDes = 10;

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, $rowTitle, '1) - SOLICITUD DE INSPECCION DE ARBOLES');

        $pdf->SetFont($font, '', $fontSizeDes);

        $colDesc = 70;
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), 'Nombre y apellido: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc, true), strtoupper($data['persona']['nombre']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'DNI: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['persona']['documento']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Domicilio Real: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['persona']['domicilio']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Domicilio: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['persona']['domicilio']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Solicita: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['solicita']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Cantidad de arboles: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['cantidad']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Ubicación de los arboles: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['ubicacion']));

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), '2) - ACTA DE INSPECCIÓN');
        getrowTitleValDesc($rowDesc, true);

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), 'Constatación del daño: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc, true), strtoupper($data['constancia_danio']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Observaciones de la Inspección: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), $data['observacion_inspector']);

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), '3) - AUTORIZADO');
        getrowTitleValDesc($rowDesc, true);

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), 'Cantidad de arboles autorizada: ');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc, true), strtoupper($data['cantidad_autorizado']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Tipo de solicitud:');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['tipo']));
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Inspector:');
        $pdf->Text($colDesc, getrowTitleValDesc($rowDesc), strtoupper($data['inspector']['nombre']));

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), '4) - OBLIGACIONES DEL SOLICITANTE');

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getrowTitleValTitle($rowTitle, true), 'En caso de autorizarse deberá cumplimentar con lo siguiente:');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.0 - PODAR el árbol por su cuenta');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.1 - EXTRAER el árbol por su cuenta');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.2 - RETIRAR todos los materiales resultantes de la extracción o poda (tocon, ramas, escombros, etc.)');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.3 - NO REPONER arboles');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.4 - En caso de no reponer los árboles indicados será pasible de multa, de acuerdo a  ORD. 10.009/12028 y cód. de faltas.');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'ESPECIES PERMITIDAS:');

        $col = 30;
        $rowTitle1 = getrowTitleValTitle($rowTitle);
        $rowTitle2 = $rowTitle1;
        $rowTitle3 = $rowTitle1;

        $pdf->Text($col, $rowTitle, '4.5.1 - Acer Negundo');
        $pdf->Text($col, getrowTitleValTitle($rowTitle), '4.5.2 - Acacia de Constantinopla');
        $pdf->Text($col, getrowTitleValTitle($rowTitle), '4.5.3 - Crespón');

        $pdf->Text($col + 55, $rowTitle2, '4.5.4 - Ciruelo de Jardín');
        $pdf->Text($col + 55, getrowTitleValTitle($rowTitle2), '4.5.5 - Fresno Americano');
        $pdf->Text($col + 55, getrowTitleValTitle($rowTitle2), '4.5.6 - Fresno Europeo');

        $pdf->Text($col + 110, $rowTitle3, '4.5.7 - Lagerstroemia Indica');
        $pdf->Text($col + 110, getrowTitleValTitle($rowTitle3), '4.5.8 - Paraiso Sombrilla');
        $pdf->Text($col + 110, getrowTitleValTitle($rowTitle3), '4.5.9 - Mora Híbrida');

        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.6 - ADOPTAR las medidas de seguridad necesarias para la realización de los trabajos, y hacerse responsable de');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'eventuales daños y perjuicios que los trabajos pudieran provocar a bienes y/o personas');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.7 - Los servicios de poda y extracción deberán ser realizados por un podador habilitado por la Municipalidad de');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), 'Neuquén, según ORD. 13430.');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '4.8 - El listado de podadores habilitados se encuentra en www.neuquencapital.gov.ar');
        $pdf->Text(15, getrowTitleValTitle($rowTitle), '5.0 - El PERMISO de Extracción tiene validez desde la fecha de entrega hasta el 31 de diciembre del corriente año');

        $pdf->Text(155, getrowTitleValTitle($rowTitle, true), 'Queda debidamente notificado');

        // close and output PDF document
        $pdf->Output(ADJUNTOS_PATH . $fileName, 'F');
        /* $pdf->Output($fileName . '.pdf', 'D'); */
    }

    public static function delete($id)
    {
        $data = new Arb_Solicitud();
        $data = $data->delete($id);

        if (!$data instanceof ErrorException) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }
}
