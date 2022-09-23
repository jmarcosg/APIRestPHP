<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\Weblogin;
use ErrorException;

class LoginController
{
    use SqlQuery;

    public static function getUserData($user, $pass)
    {
        $userData = self::fetchUserData($user, $pass);

        if ($userData instanceof ErrorException) {
            Weblogin::saveLog($userData->getMessage(), __CLASS__, __FUNCTION__);
            return $userData;
        }

        if ($userData && $userData->value && !$userData->error) {
            $data = [
                'authenticationToken' => $userData->value->authenticationToken,
                'profile' => $userData->value->profile,
                'apps' => $userData->value->apps
            ];

            $dni = $userData->value->profile->documento;

            $referenciaId = $userData->value->profile->wapUsuarioID;
            $fetch = self::viewFetch($referenciaId, $dni);

            if (!$fetch instanceof ErrorException) {
                $data['fetch'] = self::viewFetch($referenciaId, $dni);
                sendRes($data);
            } else {
                Weblogin::saveLog($fetch, __CLASS__, __FUNCTION__);
                sendRes(null, $fetch->getMessage(), $_GET);
            }
        } else {
            $error = new ErrorException($userData->error);
            Weblogin::saveLog($error->getMessage(), __CLASS__, __FUNCTION__);
            return $error;
        }

        exit;
    }

    public static function fetchUserData($user, $pass)
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

    /** Obtenemos los datos del legajo en funcion del sexo y documento */
    public static function getLegajoData()
    {
        $sexo = $_GET['sexo'];
        $doc =  $_GET['doc'];

        $model = new Weblogin();

        $sql = self::datosLegajo($sexo, $doc);
        $data = $model->executeSqlQuery($sql);

        if ($data) {
            sendRes($data);
        } else {
            $error = new ErrorException("Problema al obtener el legajo | genero: $sexo | documento: $doc");
            Weblogin::saveLog($error, __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        };
        exit;
    }

    /** Obtenemos los datos del acarreo */
    public static function getAcarreoData()
    {
        $id = $_GET['id'];

        $model = new Weblogin();

        $sql = self::datosAccareo($id);
        $data = $model->executeSqlQuery($sql);

        if ($data && !$data instanceof ErrorException) {
            sendRes($data);
        } else {
            $error = new ErrorException("Problema al obtener los datos del accareo | id: $id");
            Weblogin::saveLog($error, __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        };
        exit;
    }
}
