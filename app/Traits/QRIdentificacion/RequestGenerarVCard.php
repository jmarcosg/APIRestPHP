<?php

namespace App\Traits\QRIdentificacion;

class RequestGenerarVCard
{
    public static function generateVcard($params)
    {
        return utf8_encode(
            'BEGIN:VCARD
            VERSION:4.0
            N:;' . $params['nombre'] . ';;;
            FN:' . $params['apellido'] . '
            EMAIL:' . $params['email'] . '
            ORG:' . $params['cargo'] . '
            TEL:' . $params['tel'] . '
            END:VCARD'
        );
    }
}
