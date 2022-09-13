<?php

namespace App\Controllers\Common;

use Exception;

class LoginController
{
    public static function getUserByToken($token)
    {
        try {
            $senssionKey = self::getSessionKey($token);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => WS_WEBLOGIN . $senssionKey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_SSL_VERIFYPEER => false,
            ));

            $response = curl_exec($curl);
            $response = (object)json_decode($response, true);

            curl_close($curl);

            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function isLogin($token)
    {
        $user = self::getUserByToken($token);
        return $user->securityToken != null;
    }

    private static function getSessionKey($token)
    {
        $sessionKey = explode('%', $token)[1];
        return $sessionKey;
    }
}
