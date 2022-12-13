<?php

namespace App\Traits\QRIdentificacion;

trait RequestGenerarQR
{
    public static function sendRequest($param)
    {
        if (ENV == "local") {
            $urlQR = "http://localhost/APIRestPHP/public/views/QRIdentificacion/index.php?token=$param[qr_token]";
            $urlApi = "http://200.85.183.194:90/apps/generador_credenciales_api/api/index.php/credencial/generarCUERRE";
        } else {
            $urlQR = (PROD == "true") ? "https://weblogin.muninqn.gov.ar/apps/tarjetas_digitales/index.html#/tarjeta-de-contacto/?token=$param[qr_token]" : "http://200.85.183.194:90/apps/tarjetas_digitales/index.html#/tarjeta-de-contacto/?token=$param[qr_token]";
            $urlApi = (PROD == "true") ? "https://weblogin.muninqn.gov.ar/apps/generador_credenciales_api/api/index.php/credencial/generarCUERRE" : "http://200.85.183.194:90/apps/generador_credenciales_api/api/index.php/credencial/generarCUERRE";
        }

        $postParams = [
            "SESSIONKEY" => $param["sessionkey"],
            "idSolicitud" => $param["id_solicitud"],
            'urlQR' => $urlQR,
            'path' => $param['qr_path']
        ];

        // echo json_encode($postParams);
        $postHeaders = ["Content-Type: application/json"];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $urlApi,
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
}
