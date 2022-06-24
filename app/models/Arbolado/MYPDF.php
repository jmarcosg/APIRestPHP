<?php

namespace App\Models\Arbolado;

use TCPDF;

class MYPDF extends TCPDF
{
    public function ColoredTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(58, 143, 218);
        $this->SetTextColor(255);
        $this->SetDrawColor(58, 143, 218);
        $this->SetLineWidth(0.3);
        // Header
        $w = array(10, 19, 85, 38, 38);
        $num_headers = count($header);
        $this->SetFont('helvetica', '', 11);
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('helvetica', '', 10);
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'R', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row[3], 'LR', 0, 'L', $fill);
            $this->Cell($w[4], 6, $row[4], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
