<?php

namespace App\Traits\LicenciaComercial;

use App\Controllers\LicenciaComercial\Lc_DocumentoController;
use App\Controllers\LicenciaComercial\Lc_SolicitudRubroController;

use TCPDF;

trait Reportes
{
    public static function getSolicitudPdf($id)
    {
        $data = self::getSolicitudByQuery("id = $id");
        $rubro = new Lc_SolicitudRubroController();
        $data['rubros'] = $rubro->getRubrosBySolicitud($data['id']);

        $documento = new Lc_DocumentoController();
        $data['documentos'] = $documento->getDocumentosSolicitados($data['id']);

        $data = utf8ize($data);

        $descripcion  = self::getArrayWord($data['descripcion_actividad'], 14);

        $nro_expediente = $data['nro_expediente'];
        $nro_licencia = $data['nro_licencia'];
        $pdf = new TCPDF('p', 'mm', array(216, 356));

        // set document information
        $pdf->SetCreator('Municipalidad de Neuquén');
        $pdf->SetAuthor('Municipalidad de Neuquén');
        $pdf->SetTitle('Solicitud - ' . $id . date('d/m/Y'));
        $pdf->SetSubject('Listado Podadores');
        $pdf->SetKeywords('Listado Podadores');

        $pdf->AddPage();

        $bannerUrl = ROOT_PATH . 'public/assets/membrete_01.jpg';
        $pdf->Image($bannerUrl, 25, 15, 160, 16.30, 'JPG');

        $initRow = 40;
        $pdf->SetFont('helvetica', '', 13);
        $pdf->Text(15, $initRow, "SOLICITUD N°: $id");
        $pdf->Text(130, $initRow, "EXPEDIENTE N°: $nro_expediente");

        $initRow += 7;
        $pdf->Text(130, $initRow, "LICENCIA N°: $nro_licencia");

        $fechaFinalizado = date("d/m/Y", strtotime($data['fecha_finalizado']));
        $pdf->Text(15, $initRow, "FECHA: $fechaFinalizado");

        $initRow += 13;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Text(15, $initRow, "DATOS PERSONALES");

        $inc = 5.5;
        $row = $initRow + 15;

        if ($data['personaTercero']) {

            /* Solicitante - Titular de la licencia */
            $row = $initRow + 15;
            $col = 15;
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Text($col, $row - 7, "TITULAR DE LA LICENCIA");
            $pdf->SetFont('helvetica', 'B', 9);

            $pdf->Text($col, $row, "Nombre:");
            $pdf->Text($col, self::getRow($row, $inc), "DNI:");
            $pdf->Text($col, self::getRow($row, $inc), "CUIT:");
            $pdf->Text($col, self::getRow($row, $inc), "Domicilio:");
            $pdf->Text($col, self::getRow($row, $inc), "Teléfono:");
            $pdf->Text($col, self::getRow($row, $inc), "Email:");

            /* DEBEMOS CARGAR LOS DATOS DE UN TERCERO */
            $row = $initRow + 15;
            $col = $col + 18;
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Text($col, $row, $data['personaTercero']['nombre']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['documento']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['cuil']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['domicilio']);
            $pdf->Text($col, self::getRow($row, $inc), $data['telefono']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['email']);

            /* Solicitante */
            $col = 110;
            self::datosSolicitantePorCol($col, $data, $pdf, $initRow, $inc, 'SOLICITANTE');
        } else {
            /* Solicitante */
            $row = $initRow + 15;
            $col = 15;
            if ($data['tipo_persona'] === 'fisica') {
                $row = $initRow + 15;
                $col = 15;
                $pdf->SetFont('helvetica', 'B', 9);
                self::datosSolicitantePorCol($col, $data, $pdf, $initRow, $inc, 'TITULAR DE LA LICENCIA');
            } else {
                $col = 15;
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Text($col, $row - 7, "TITULAR DE LA LICENCIA");
                $pdf->SetFont('helvetica', 'B', 9);

                $pdf->Text($col, $row, "CUIT:");
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Text($col + 18, $row, $data['cuit']);
                $col = 110;
                self::datosSolicitantePorCol($col, $data, $pdf, $initRow, $inc, 'SOLICITANTE');
            }
        }

        $initRow = 113.5;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Text(15, $initRow, "DATOS DE LA ACTIVIDAD");

        $row = $initRow + 9;
        $col = 15;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Text($col, $row, "DESCRIPCIÓN");

        /* $row -= $inc; */
        $pdf->SetFont('helvetica', '', 9);
        foreach ($descripcion as $value) {
            $pdf->Text($col, self::getRow($row, 4), $value);
        }

        $initRow = $row + 9;
        $row = $initRow;
        $col = 15;

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Text($col, $row, "Nomenclatura:");
        $pdf->Text($col + 110, $row, "Tiene local?");

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Text($col + 130, $row, $data['tiene_local'] == '1' ? 'SI' : 'No');

        $pdf->SetFont('helvetica', 'B', 9);
        if ($data['tiene_local'] == 1) {
            $pdf->Text($col, self::getRow($row, $inc), "Direccion comercial:");
        } else {
            $pdf->Text($col, self::getRow($row, $inc), "Direccion de notificaciones:");
        }
        $pdf->Text($col, self::getRow($row, $inc), "Metros cuadrados:");
        $pdf->Text($col, self::getRow($row, $inc), "Nombre fantasía:");

        $row = $initRow;
        $col = $col + 50;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Text($col, $row,  $data['nomenclatura']);
        if ($data['tiene_local'] == 1) {
            $pdf->Text($col, self::getRow($row, $inc), self::formatDomicilio($data['direccion_comercial']));
        } else {
            $pdf->Text($col, self::getRow($row, $inc), self::formatDomicilio($data['domicilio_particular']));
        }
        $pdf->Text($col, self::getRow($row, $inc), $data['m2']);
        $pdf->Text($col, self::getRow($row, $inc), $data['nombre_fantasia']);
        $col = 15;
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Text($col, self::getRow($row, $inc * 1.5), "Rubros:");

        $pdf->SetFont('helvetica', 'B', 9);
        foreach ($data['rubros'] as $key => $value) {
            if ($key == 0) {
                $pdf->Text($col, self::getRow($row, $inc), $value['label'] . ' *');
            } else {
                $pdf->Text($col, self::getRow($row, $inc), $value['label']);
            }
            $pdf->SetFont('helvetica', '', 9);
        }

        /* NUEVA PAGINA */
        /* $pdf->AddPage();
        $row = 20; */
        $row += 15;
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Text($col, $row, "Documentos solicitados:");
        $row += 3;
        $inc = 4;
        $pdf->SetFont('helvetica', '', 9);
        foreach ($data['documentos'] as $value) {
            $pdf->Text($col, self::getRow($row, $inc), $value['label']);
        }

        $pdf->SetFont('helvetica', 'B', 10);

        $pdf->Text($col, self::getRow($row, 10), 'Notas de Catastro: ' . ($data['notas_catastro'] ? 'SI' : 'NO'));
        $pdf->Text(65, $row, 'Notas de Ambiental: ' . ($data['notas_ambiente'] ? 'SI' : 'NO'));

        $row = 330;

        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->Text(120, $row, 'Firma del agente municipal');
        $pdf->Line(15, $row, 95, $row);
        /* $pdf->RoundedRect(45, $row, 75, 30, 6.50, '0000'); */
        
        $pdf->Text(15, $row, 'Firma y acalaración del contribuyente');
        $pdf->Line(120, $row, 200, $row);
        /* $pdf->RoundedRect(185, $row, 75, 30, 6.50, '0000'); */

        /* Preparacion del archivo */
        $base64 = $pdf->Output('Listado_podadores.pdf', 'E');
        $base64 = explode('"', $base64);
        $base64 = trim($base64[count($base64) - 1]);
        echo sendRes(['pdf' => $base64]);
        exit;
    }

    public static function getRow(&$row, $int)
    {
        $row = $row + $int;
        return $row;
    }

    public static function getArrayWord($string, $palabras)
    {
        $cantidad_palabras = $palabras;

        $palabras_arreglo = explode(" ", $string);

        $cantidad_lineas =  ceil(count($palabras_arreglo) / $cantidad_palabras);

        $arreglo = [];
        $inicio = 0;
        for ($i = 0; $i < $cantidad_lineas; $i++) {
            $arreglo[] =  implode(" ", array_slice($palabras_arreglo, $inicio, $cantidad_palabras));
            $inicio += $cantidad_palabras;
        }

        return $arreglo;
    }

    public static function formatDomicilio($string)
    {
        if (str_contains($string, ';')) {
            $dom = explode(";", $string);
            $direccion = $dom[0] . ' ' . $dom[1] . ', ';

            if ($dom[2] !== '-') {
                $direccion .= 'Mzn ' . $dom[2] . ', ';
            }

            if ($dom[3] !== '-') {
                $direccion .= 'Lote ' . $dom[3] . ', ';
            }

            if ($dom[4] !== '-') {
                $direccion .= 'Piso ' . $dom[4] . ', ';
            }

            if ($dom[5] !== '-') {
                $direccion .= 'Depto ' . $dom[5] . ', ';
            }

            return $direccion .  $dom[6];
        }

        return $string;
    }

    public static function datosSolicitantePorCol($col, $data, $pdf, $initRow, $inc, $title)
    {
        $row = $initRow + 15;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Text($col, $row - 7, $title);

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Text($col, $row, "Nombre:");
        $pdf->Text($col, self::getRow($row, $inc), "DNI:");
        $pdf->Text($col, self::getRow($row, $inc), "CUIT:");
        $pdf->Text($col, self::getRow($row, $inc), "Domicilio:");
        $pdf->Text($col, self::getRow($row, $inc), "Teléfono:");
        $pdf->Text($col, self::getRow($row, $inc), "Email:");

        $row = $initRow + 15;
        $col = $col + 18;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Text($col, $row,  $data['personaInicio']['nombre']);
        $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['documento']);
        $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['cuil']);
        $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['domicilio']);
        $pdf->Text($col, self::getRow($row, $inc), $data['telefono']);
        $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['email']);
    }
}
