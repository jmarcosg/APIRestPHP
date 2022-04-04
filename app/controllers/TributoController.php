<?php

namespace App\Controllers;

use App\Models\Tributo;

class TributoController
{
    public function save($res)
    {
        $tributo = new Tributo();
        $tributo->set($res);
        return $tributo->save();
    }
}
