<?php

namespace App\Controllers;

use App\Models\Tributo;
use ErrorException;

class TributoController
{
    public function save($res)
    {
        $tributo = new Tributo();
        $tributo->set($res);
        return $tributo->save();
    }

    public function sendEmailMensual($res)
    {
        $tributo = new Tributo();
        return $tributo->sendEmailMensual($res);
    }

    public function sendEmailSemestral($res)
    {
        $tributo = new Tributo();
        return $tributo->sendEmailSemestral($res);
    }
}
