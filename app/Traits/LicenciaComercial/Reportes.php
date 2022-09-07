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

        $descripcion  = self::getArrayWord($data['descripcion'], 14);

        $nro_expediente = $data['nro_expediente'];
        $nro_licencia = $data['nro_licencia'];
        $pdf = new TCPDF('A4', 'mm');

        // set document information
        $pdf->SetCreator('Municipalidad de Neuquén');
        $pdf->SetAuthor('Municipalidad de Neuquén');
        $pdf->SetTitle('Solicitud - ' . $id . date('d/m/Y'));
        $pdf->SetSubject('Listado Podadores');
        $pdf->SetKeywords('Listado Podadores');

        $pdf->AddPage();

        $initRow = 45;
        $pdf->SetFont('helvetica', '', 13);
        $pdf->Text(15, $initRow, "SOLICITUD N°: $id");
        $pdf->Text(69, $initRow, "EXPEDIENTE N°: $nro_expediente");
        $pdf->Text(138, $initRow, "LICENCIA N°: $nro_licencia");

        $initRow += 13;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Text(15, $initRow, "DATOS PERSONALES");

        $inc = 6;
        if ($data['personaTercero']) {

            /* Solicitante - Titular de la licencia */
            $row = $initRow + 15;
            $col = 15;
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Text($col, $row - 7, "TITULAR DE LA LICENCIA");
            $pdf->SetFont('helvetica', 'B', 10);

            $pdf->Text($col, $row, "Nombre:");
            $pdf->Text($col, self::getRow($row, $inc), "DNI:");
            $pdf->Text($col, self::getRow($row, $inc), "CUIL:");
            $pdf->Text($col, self::getRow($row, $inc), "Domicilio:");
            $pdf->Text($col, self::getRow($row, $inc), "Teléfono:");
            $pdf->Text($col, self::getRow($row, $inc), "Email:");

            /* DEBEMOS CARGAR LOS DATOS DE UN TERCERO */
            $row = $initRow + 15;
            $col = $col + 18;
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Text($col, $row, firstUpper($data['personaTercero']['nombre']));
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['documento']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['cuil']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['domicilio']);
            $pdf->Text($col, self::getRow($row, $inc), $data['telefono']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaTercero']['email']);

            /* Solicitante */
            $row = $initRow + 15;
            $col = 110;
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Text($col, $row - 7, "SOLICITANTE");

            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Text($col, $row, "Nombre:");
            $pdf->Text($col, self::getRow($row, $inc), "DNI:");
            $pdf->Text($col, self::getRow($row, $inc), "CUIL:");
            $pdf->Text($col, self::getRow($row, $inc), "Domicilio:");
            $pdf->Text($col, self::getRow($row, $inc), "Teléfono:");
            $pdf->Text($col, self::getRow($row, $inc), "Email:");

            $row = $initRow + 15;
            $col = $col + 18;
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Text($col, $row,  firstUpper($data['personaInicio']['nombre']));
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['documento']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['cuil']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['domicilio']);
            $pdf->Text($col, self::getRow($row, $inc), $data['telefono']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['email']);
        } else {
            /* Solicitante */
            $row = $initRow + 15;
            $col = 15;
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Text($col, $row - 7, "TITULAR DE LA LICENCIA");

            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Text($col, $row, "Nombre:");
            $pdf->Text($col, self::getRow($row, $inc), "DNI:");
            $pdf->Text($col, self::getRow($row, $inc), "CUIL:");
            $pdf->Text($col, self::getRow($row, $inc), "Domicilio:");
            $pdf->Text($col, self::getRow($row, $inc), "Teléfono:");
            $pdf->Text($col, self::getRow($row, $inc), "Email:");

            $row = $initRow + 15;
            $col = $col + 18;
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Text($col, $row,  firstUpper($data['personaInicio']['nombre']));
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['documento']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['cuil']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['domicilio']);
            $pdf->Text($col, self::getRow($row, $inc), $data['telefono']);
            $pdf->Text($col, self::getRow($row, $inc), $data['personaInicio']['email']);
        }

        $initRow = $row  + 13;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Text(15, $initRow, "DATOS DE LA ACTIVIDAD");

        $row = $initRow + 10;
        $col = 15;
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Text($col, $row, "DESCRIPCIÓN");

        /* $row -= $inc; */
        $pdf->SetFont('helvetica', '', 10);
        foreach ($descripcion as $value) {
            $pdf->Text($col, self::getRow($row, 4), $value);
        }

        $initRow = $row + 10;
        $row = $initRow;
        $col = 15;

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Text($col, $row, "Nomenclatura:");

        if ($data['tiene_local'] == 1) {
            $pdf->Text($col, self::getRow($row, $inc), "Direccion comercial:");
        } else {
            $pdf->Text($col, self::getRow($row, $inc), "Direccion de notificaciones:");
        }
        $pdf->Text($col, self::getRow($row, $inc), "Metros cuadrados:");
        $pdf->Text($col, self::getRow($row, $inc), "Nombre fantasía:");
        $pdf->Text($col, self::getRow($row, $inc), "Tiene local?");

        $row = $initRow;
        $col = $col + 50;
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Text($col, $row,  firstUpper($data['nomenclatura']));
        if ($data['tiene_local'] == 1) {
            $pdf->Text($col, self::getRow($row, $inc), self::formatDomicilio($data['direccion_comercial']));
        } else {
            $pdf->Text($col, self::getRow($row, $inc), self::formatDomicilio($data['domicilio_particular']));
        }
        $pdf->Text($col, self::getRow($row, $inc), $data['m2']);
        $pdf->Text($col, self::getRow($row, $inc), $data['nombre_fantasia']);
        $pdf->Text($col, self::getRow($row, $inc), $data['tiene_local'] == '1' ? 'SI' : 'No');
        $col = 15;
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Text($col, self::getRow($row, $inc * 2), "Rubros:");

        $pdf->SetFont('helvetica', 'B', 9);
        foreach ($data['rubros'] as $value) {
            $pdf->Text($col, self::getRow($row, $inc), $value['label']);
            $pdf->SetFont('helvetica', '', 9);
        }

        /* NUEVA PAGINA */
        $pdf->AddPage();
        $row = 20;
        $inc = 4;
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Text($col, $row, "Documentos solicitados:");
        $row += 6;
        $pdf->SetFont('helvetica', '', 9);
        foreach ($data['documentos'] as $value) {
            $pdf->Text($col, self::getRow($row, $inc), $value['label']);
        }

        $pdf->SetFont('helvetica', 'B', 11);
        $descRow = $row;
        $pdf->Text($col, self::getRow($row, 10), 'Notas de Catastro: ');
        $pdf->Text($col, self::getRow($row, 6), 'Notas de Ambiental: ');

        if ($data['notas_catastro']) {
            $pdf->Text(60, self::getRow($descRow, 10), 'SI');
        } else {
            $pdf->Text(60, self::getRow($descRow, 10), 'NO');
        }

        if ($data['notas_ambiente']) {
            $pdf->Text(60, self::getRow($descRow, 6), 'SI');
        } else {
            $pdf->Text(60, self::getRow($descRow, 6), 'NO');
        }

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
}
