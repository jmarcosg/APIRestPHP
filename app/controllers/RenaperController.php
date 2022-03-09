<?php

namespace App\Controllers;

use App\Models\Renaper;

class RenaperController
{
    public function getData($gender, $dni)
    {
        $renaper = new Renaper();
        return $renaper->getData($gender, $dni);
    }
}
