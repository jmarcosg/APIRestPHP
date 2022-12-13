<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Traits\CredencialesEmpleados\RequestGenerarQR;
use App\Models\CredencialesEmpleados\CREDEMP_Codigo_QR;
use ErrorException;

class CREDEMP_CodigoQRController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'CREDEMP_codigo_qr';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Codigo_QR();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $cantQrs = count(self::index()) + 1;
        $qrToken = md5(microtime(true));
        $res['id_solicitud'] = $cantQrs;
        $res['qr_token'] = $qrToken;
        $output = RequestGenerarQR::sendRequest($res);

        if ($output['code'] == "200") {
            $usuario = CREDEMP_UsuarioController::index(['email' => $res['mailUsuario']])[0];
            $qrData = [
                'id_usuario' => $usuario['id'],
                'id_persona_identificada' => 0,
                'qr_token' => $qrToken,
                'qr_path' => "QR-$cantQrs.png"
            ];

            $data = new CREDEMP_Codigo_QR();
            $data->set($qrData);
            $data = $data->save();
        } else {
            $data = new ErrorException();
        }

        return $data;
    }


    public static function update($data, $id)
    {
        $qr = new CREDEMP_Codigo_QR();
        return $qr->update($data, $id);
    }
}
