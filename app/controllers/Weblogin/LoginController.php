<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\Weblogin;
use ErrorException;

class LoginController
{
    use SqlTrait, FormatTrait;

    public static function getUserData($user, $pass)
    {
        $userData = self::fetchUserData($user, $pass);

        if ($userData instanceof ErrorException) {
            Weblogin::saveLog($userData->getMessage() . "| Usuario: $user | Contraseña: $pass", __CLASS__, __FUNCTION__);
            sendRes(null, $userData->getMessage(), null);
            exit;
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

            sendResError($fetch, 'Hubo un error al realizar el inicio de sesion');

            /* Weblogin::saveLog($fetch, __CLASS__, __FUNCTION__); */
            /* sendRes(null, $fetch->getMessage(), $_GET); */

            $data['fetch'] = $fetch;

            sendRes($data);
        } else {
            $error = new ErrorException($userData->error);
            Weblogin::saveLog($error->getMessage(), __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
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
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json']
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);
            if ($response == null || $response->error != null) {
                return new ErrorException('Problema con el inicio de sesion');
            }
            return $response;
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

        if ($data && !$data instanceof ErrorException) {
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

    /** Obtenemos los datos de licencia de conducir */
    public static function getLicConducirData()
    {
        $id = $_GET['id'];

        $model = new Weblogin();

        $sql = self::datosLicConducir($id);
        $data = $model->executeSqlQuery($sql);

        if ($data && !$data instanceof ErrorException) {
            $data = self::formatLicConducir($data);
            sendRes($data);
        } else {
            $error = new ErrorException("Problema al obtener los datos de licencia de conducir | id: $id");
            Weblogin::saveLog($error, __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        };
        exit;
    }

    /** Obtenemos los datos de licencia de conducir */
    public static function getMuniEventos()
    {
        $id = $_GET['dni'];

        $data = self::getMuniEventosFetch($id);

        if ($data && !$data instanceof ErrorException) {
            sendRes($data);
        } else {
            $error = new ErrorException("Problema al obtener los datos de muni eventos | dni: $id");
            Weblogin::saveLog($error, __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        };
        exit;
    }

    /** Obtenemos los datos de licencia de conducir */
    public static function getLibretasanitariaData()
    {
        $id = $_GET['id'];

        $model = new Weblogin();

        $sql = self::datosLibretaSanitaria($id);
        $data = $model->executeSqlQuery($sql);

        if ($data && !$data instanceof ErrorException) {
            $data = self::formatLibretaSanitaria($data);
            sendRes($data);
        } else {
            $error = new ErrorException("Problema al obtener los datos de la libreta sanitaria | id: $id");
            Weblogin::saveLog($error, __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        };
        exit;
    }

    public static function getLibretasanitariaDataDos()
    {
        $id = $_GET['id'];

        $model = new Weblogin();

        $sql = self::datosLibretaSanitaria(37216);
        $data = $model->executeSqlQuery($sql);

        if ($data && !$data instanceof ErrorException) {
            $data = self::formatLibretaSanitaria($data);
            sendRes($data);
        } else {
            $error = new ErrorException("Problema al obtener los datos de la libreta sanitaria | id: $id");
            Weblogin::saveLog($error, __CLASS__, __FUNCTION__);
            sendRes(null, $error->getMessage(), $_GET);
        };
        exit;
    }
}
