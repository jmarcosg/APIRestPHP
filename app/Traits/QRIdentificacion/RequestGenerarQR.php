<?php

namespace App\Traits\QRIdentificacion;

trait RequestGenerarQR
{
    public static function sendRequest($param)
    {
        $postParams = [
            "SESSIONKEY" => $param["sessionkey"],
            "idSolicitud" => $param["id_solicitud"],
            'urlQR' => "http://localhost:5173/?token=" . $param['qr_token'],
            'path' => $param['qr_path']
        ];

        // echo json_encode($postParams);
        $postHeaders = ["Content-Type: application/json"];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://200.85.183.194:90/apps/generador_credenciales_api/api/index.php/credencial/generarCUERRE",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => $postHeaders,
            CURLOPT_POSTFIELDS => json_encode($postParams),
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        $serverOutput = json_decode($response, true);
        $serverOutput["data"]["urlQR"] = $postParams["urlQR"];

        return $serverOutput;
    }

    public static function operacionesQR()
    {
    }
}
