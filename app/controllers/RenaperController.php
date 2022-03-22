<?php

namespace App\Controllers;

use App\Models\Renaper;
use ErrorException;

class RenaperController
{
    public function getData($gender, $dni)
    {
        $renaper = new Renaper();
        return $renaper->getData($gender, $dni);
    }

    public function getImage($gender, $dni)
    {
        $renaper = new Renaper();
        $renaper = $renaper->getData($gender, $dni);

        return [
            'imagen' => $renaper->imagen,
            'urlImagen' => $renaper->urlImagen
        ];
    }

    public function getPersonData($gender, $dni)
    {
        $renaper = new Renaper();
        $renaper = $renaper->getData($gender, $dni);

        if ($renaper instanceof ErrorException) return $renaper;

        return [
            'referenciaID' => $renaper->referenciaID,
            'documento' => $renaper->documento,
            'cuil' => $renaper->cuil,
            'razonSocial' => $renaper->razonSocial,
            'fechaDeNacimiento' => $renaper->fechaDeNacimiento,
            'edad' => $renaper->edad,
            'genero' => [
                'textID' => $renaper->genero->textID,
                'value' => $renaper->genero->value,
            ],
            'celular' => $renaper->celular,
            'correoElectronico' => $renaper->correoElectronico,
        ];
    }

    public function getTokenRenaper()
    {
        $renaper = new Renaper();
        return $renaper->getTokenRenaper();
    }
}
