<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\Weblogin;
use App\Traits\WebLogin\GettersDataTrait;
use ErrorException;

class LoginController
{
    use FormatTrait, GettersDataTrait;

    public static function getUserData($user, $pass)
    {
        $userData = self::fetchUserData($user, $pass);

        if ($userData instanceof ErrorException) {
            Weblogin::saveLog($userData->getMessage() . "| Usuario: $user | Contraseña: $pass", __CLASS__, __FUNCTION__);
            sendRes(null, $userData->getMessage(), null);
            exit;
        }

        if ($userData && $userData->value && !$userData->error) {
            $data = $userData->value;

            $fetch = self::viewFetch($data->profile->wapUsuarioID, $data->profile->documento);

            sendResError($fetch, 'Hubo un error al realizar el inicio de sesion');

            $data->fetch = $fetch;

            sendRes($data);
        } else {
            $error = new ErrorException($userData->error);
            Weblogin::saveLog($error->getMessage(), __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        }

        exit;
    }

    /** Obtiene los datos de usuario basado en sus credenciales*/
    private static function fetchUserData($user, $pass)
    {
        try {
            $postData = [
                "action" => 3,
                "credentials" => [
                    "userName" => $user,
                    "userPass" => $pass
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => BASE_WEB_LOGIN_API . 'webLogin2',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json']
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);

            if ($response == null || $response->error != null) {
                return new ErrorException("Problema con el inicio de sesion: $response->error");
            }

            return $response;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /** Obtenemos todos los datos para mostrar al usuario */
    public static function getAllData()
    {
        $ps =  json_decode($_POST['procedures_started']);
        $response = [
            'appsRecientes' => WapAppsRecientesController::getAppsRecientes($_POST['id_usuario']),
            'tramites' => WlAppController::getApps(),

            'procedures_started' => [
                'legajo' => $ps->legajo->fetch ? self::getLegajoData($_POST['genero'], $_POST['dni']) : false,
                'acarreo' => $ps->acarreo->fetch ? self::getAcarreoData($_POST['id_usuario']) : false,
                'licencia' => $ps->licencia->fetch ? self::getLicConducirData($_POST['dni']) : false,
                'muniEventos' => $ps->muniEventos->fetch ? self::getMuniEventos($_POST['dni']) : false,
                'licencia_comercial' => $ps->licencia_comercial->fetch ? self::getLicenciaComercial($_POST['id_usuario']) : false,
                'libreta' => $ps->libreta->fetch ? self::getLibretasanitariaData($_POST['id_usuario']) : false,
                'libretaDos' => $ps->libretaDos->fetch ? self::getLibretasanitariaData($_POST['id_usuario']) : false,
            ]
        ];

        sendRes($response);
    }
}
