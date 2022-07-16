<?php

namespace App\Controllers\Arbolado;

use App\Traits\Arbolado\TemplateEmailSolicitud;

use App\Models\Arbolado\Arb_Solicitud;
use App\Models\Arbolado\Arb_Audit;

use App\Models\Arbolado\MYPDF;

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

    public function getSolicitudPodaPdf($id, $fileName)
    {
        $solicitud = $this->get(['id' => $id]);

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

        $row = 60;
        function getRowVal(&$row, $isTitle = false)
        {
            if ($isTitle) {
                $row = $row  + 10;
                return $row;
            } else {
                $row = $row  + 5;
                return $row;
            }
        }

        $fontSizeTitle = 11;
        $fontSizeDes = 10;

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, $row, '1) - SOLICITUD DE INSPECCION DE ARBOLES');

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getRowVal($row, true), 'Nombre y apellido: ');
        $pdf->Text(15, getRowVal($row), 'DNI: ');
        $pdf->Text(15, getRowVal($row), 'Domicilio Real: ');
        $pdf->Text(15, getRowVal($row), 'Domicilio: ');
        $pdf->Text(15, getRowVal($row), 'Solicita: ');
        $pdf->Text(15, getRowVal($row), 'Cantidad de arboles: ');
        $pdf->Text(15, getRowVal($row), 'Ubicación de los arboles: ');

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, getRowVal($row, true), '2) - ACTA DE INSPECCIÓN');

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getRowVal($row, true), 'Constatación del daño: ');
        $pdf->Text(15, getRowVal($row), 'OBservaciones de la Inspección: ');

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, getRowVal($row, true), '3) - AUTORIZADO');

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getRowVal($row, true), 'Cantidad de arboles autorizada: ');
        $pdf->Text(15, getRowVal($row), 'Tipo de solicitud:');
        $pdf->Text(15, getRowVal($row), 'Inspector:');

        $pdf->SetFont($font, 'b', $fontSizeTitle);
        $pdf->Text(15, getRowVal($row, true), '4) - OBLIGACIONES DEL SOLICITANTE');

        $pdf->SetFont($font, '', $fontSizeDes);
        $pdf->Text(15, getRowVal($row, true), 'En caso de autorizarse deberá cumplimentar con lo siguiente:');
        $pdf->Text(15, getRowVal($row), '4.0 - PODAR el árbol por su cuenta');
        $pdf->Text(15, getRowVal($row), '4.1 - EXTRAER el árbol por su cuenta');
        $pdf->Text(15, getRowVal($row), '4.2 - RETIRAR todos los materiales resultantes de la extracción o poda (tocon, ramas, escombros, etc.)');
        $pdf->Text(15, getRowVal($row), '4.3 - NO REPONER arboles');
        $pdf->Text(15, getRowVal($row), '4.4 - En caso de no reponer los árboles indicados será pasible de multa, de acuerdo a  ORD. 10.009/12028 y cód. de faltas.');
        $pdf->Text(15, getRowVal($row), 'ESPECIES PERMITIDAS:');

        $col = 30;
        $row1 = getRowVal($row);
        $row2 = $row1;
        $row3 = $row1;

        $pdf->Text($col, $row, '4.5.1 - Acer Negundo');
        $pdf->Text($col, getRowVal($row), '4.5.2 - Acacia de Constantinopla');
        $pdf->Text($col, getRowVal($row), '4.5.3 - Crespón');

        $pdf->Text($col + 55, $row2, '4.5.4 - Ciruelo de Jardín');
        $pdf->Text($col + 55, getRowVal($row2), '4.5.5 - Fresno Americano');
        $pdf->Text($col + 55, getRowVal($row2), '4.5.6 - Fresno Europeo');

        $pdf->Text($col + 110, $row3, '4.5.7 - Lagerstroemia Indica');
        $pdf->Text($col + 110, getRowVal($row3), '4.5.8 - Paraiso Sombrilla');
        $pdf->Text($col + 110, getRowVal($row3), '4.5.9 - Mora Híbrida');

        $pdf->Text(15, getRowVal($row), '4.6 - ADOPTAR las medidas de seguridad necesarias para la realización de los trabajos, y hacerse responsable de');
        $pdf->Text(15, getRowVal($row), 'eventuales daños y perjuicios que los trabajos pudieran provocar a bienes y/o personas');
        $pdf->Text(15, getRowVal($row), '4.7 - Los servicios de poda y extracción deberán ser realizados por un podador habilitado por la Municipalidad de');
        $pdf->Text(15, getRowVal($row), 'Neuquén, según ORD. 13430.');
        $pdf->Text(15, getRowVal($row), '4.8 - El listado de podadores habilitados se encuentra en www.neuquencapital.gov.ar');
        $pdf->Text(15, getRowVal($row), '5.0 - El PERMISO de Extracción tiene validez desde la fecha de entrega hasta el 31 de diciembre del corriente año');

        $pdf->Text(155, getRowVal($row, true), 'Queda debidamente notificado');

        // close and output PDF document
        $pdf->Output(ADJUNTOS_PATH . $fileName, 'F');
    }

    public function delete($id)
    {
        $data = new Arb_Solicitud();
        return $data->delete($id);
    }
}
