<?php

namespace App\Controllers\Weblogin;

use ErrorException;

class LoginController
{
    use SqlQuery;

    protected $logPath = 'v1/login';

    public function getUserData($user, $pass)
    {
        $userData = $this->fetchUserData($user, $pass);

        if ($userData instanceof ErrorException) return $userData;

        if ($userData && $userData->value && !$userData->error) {
            $data = [
                'authenticationToken' => $userData->value->authenticationToken,
                'profile' => $userData->value->profile,
                'apps' => $userData->value->apps
            ];

            $dni = $userData->value->profile->documento;

            $referenciaId = $userData->value->profile->wapUsuarioID;
            $data['fetch'] = $this->viewFetch($referenciaId, $dni);

            return $data;
        } else {
            return new ErrorException($userData->error);
        }
    }

    public function fetchUserData($user, $pass)
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
