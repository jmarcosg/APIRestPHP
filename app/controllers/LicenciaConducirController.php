<?php

namespace App\Controllers;

use App\Models\LicenciaConducir;

class LicenciaConducirController
{
    public function getByReferenciaId($id)
    {
        $licencia = new LicenciaConducir();
        return $licencia->getByDocumento($id);
    }
}
