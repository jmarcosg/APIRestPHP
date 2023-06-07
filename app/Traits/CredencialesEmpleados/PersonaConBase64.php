<?php

namespace App\Traits\CredencialesEmpleados;

use App\Controllers\CredencialesEmpleados\CREDEMP_CodigoQRController;
use App\Controllers\CredencialesEmpleados\CREDEMP_PersonaController;
use App\Models\Renaper;

trait PersonaConBase64
{
    public static function devolverPersonaConBase64($data)
    {
        $persona = CREDEMP_PersonaController::index(['id' => $data['id']])[0];

        $renaper = new Renaper();
        $personImg = $renaper->getData($persona['genero'], $persona['dni']);

        $qr = CREDEMP_CodigoQRController::index(['id_persona_identificada' => $data['id']])[0];
        $number = explode(".", explode("-", $qr['qr_path'])[1])[0];
        $base64path = FILE_PATH . "$number/";
        $imgQrBase64 = getBase64String($base64path . $qr['qr_path'], $qr['qr_path']);

        $imagesBase64 = [$imgQrBase64, $personImg->imagen];
        return $imagesBase64;
    }
}
