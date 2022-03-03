<?php

namespace App\Models;

use ErrorException;

class Login
{
    public function getUserData($user, $pass)
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
                CURLOPT_URL => WEBLOGIN2,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json']
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            if ($response) {
                return json_decode($response);
            } else {
                return new ErrorException('Problema con el inicio de sesion');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
