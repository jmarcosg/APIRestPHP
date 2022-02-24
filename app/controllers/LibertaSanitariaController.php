<?php

namespace App\Controllers;

use App\Models\LibretaSanitaria;

class LibertaSanitariaController
{
    public function getSolicitudesWhereId($id)
    {
        $libreta = new LibretaSanitaria();
        return $libreta->getSolicitudesWhereId($id);
    }
}
