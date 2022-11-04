<?php

namespace App\Traits\QRIdentificacion;

use App\Controllers\QRIdentificacion\QRI_CodigoQRController;

trait PersonasConBase64
{
    public static function devolverArrayConBase64($data)
    {
        $arrayConBase64 = [];
        foreach ($data as $persona) {
            $qr = QRI_CodigoQRController::index(['id_persona_identificada' => $persona['id']])[0];
            $path = FILE_PATH . "qr-identificacion/$qr[id]/QR-$qr[id].png";
            $persona['img_path'] = getBase64String($path, "QR-$qr[id].png");
            $persona['token'] = $qr['qr_token'];
            array_push($arrayConBase64, $persona);
        }
        return $arrayConBase64;
    }
}
