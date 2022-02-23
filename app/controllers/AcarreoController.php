<?php

namespace App\Controllers;

use App\Models\Acarreo;

class AcarreoController
{
    public function getByReferenciaId($id)
    {
        $acarreo = new Acarreo();
        return $acarreo->getByReferenciaId($id);
    }
}
