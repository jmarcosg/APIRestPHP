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
                return new ErrorException('Problema con el inicio de sesion');
            }
            return $response;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function getAllData()
    {
        $ps =  json_decode($_POST['procedures_started']);
        $response = [
            'appsRecientes' => WapAppsRecientesController::getAppsRecientes($_POST['id_usuario']),
            'legajo' => $ps->legajo->fetch ? self::getLegajoData($_POST['genero'], $_POST['dni']) : false,
            'acarreo' => $ps->acarreo->fetch ? self::getAcarreoData($_POST['id_usuario']) : false,
            'licencia' => $ps->licencia->fetch ? self::getLicConducirData($_POST['dni']) : false,
            'muniEventos' => $ps->muniEventos->fetch ? self::getMuniEventos($_POST['dni']) : false,
            'licencia_comercial' => $ps->licencia_comercial->fetch ? self::getLicenciaComercial($_POST['id_usuario']) : false,
            'libreta' => $ps->libreta->fetch ? self::getLibretasanitariaData($_POST['id_usuario']) : false,
            'libretaDos' => $ps->libretaDos->fetch ? self::getLicenciaComercial($_POST['dni']) : false,
        ];

        sendRes($response);
    }

    /** Obtenemos los datos del legajo en funcion del sexo y documento */
    public static function getLegajoData($genero, $dni)
    {
        $model = new Weblogin();

        $sql = self::datosLegajo($genero, $dni);
        $legajo = $model->executeSqlQuery($sql);

        $legajo = self::formatData($legajo, 'Problame al obtener el legajo');

        return $legajo;
    }

    /** Obtenemos los datos del acarreo */
    public static function getAcarreoData($id_usuario)
    {
        $model = new Weblogin();

        $sql = self::datosAccareo($id_usuario);
        $acarreo = $model->executeSqlQuery($sql);

        $acarreo = self::formatData($acarreo, 'Problame al obtener el acarreo');
        return $acarreo;
    }

    /** Obtenemos los datos de licencia de conducir */
    public static function getLicConducirData($dni)
    {
        $model = new Weblogin();

        $sql = self::datosLicConducir($dni);
        $licencia = $model->executeSqlQuery($sql);

        $licencia = self::formatData($licencia, 'Problame al obtener la licencia de conducir');
        return $licencia;
    }

    public static function getLicenciaComercial($id_usuario)
    {
        $model = new Weblogin();

        $sql = self::datosLicComercial($id_usuario);
        $licComercial = $model->executeSqlQuery($sql, false);

        if ($licComercial && !$licComercial instanceof ErrorException) {
            usort($licComercial, function ($a, $b) {
                return intval($a['historial']) < intval($b['historial']);
            });
        }

        $licComercial = self::formatData($licComercial, 'Problema al obtener la licencia comercial', 'No se encontraron licencias comerciales');
        return $licComercial;
    }

    /** Obtenemos los datos de de MuniEventos */
    public static function getMuniEventos($dni)
    {
        $muniEventos = self::getMuniEventosFetch($dni);
        $muniEventos = self::formatData($muniEventos, 'Problame al obtener los eventos');
        return $muniEventos;
    }

    /** Obtenemos los datos de licencia de conducir */
    public static function getLibretasanitariaData($id_usuario)
    {
        $model = new Weblogin();

        $sql = self::datosLibretaSanitaria($id_usuario);
        $libretaSanitaria = $model->executeSqlQuery($sql);

        $libretaSanitaria = self::formatData($libretaSanitaria, 'Problame al obtener carnet de manipulacion');
        return $libretaSanitaria;
    }

    public static function getLibretasanitariaDataDos()
    {

        $model = new Weblogin();

        $sql = self::datosLibretaSanitaria(37216);
        $libretaSanitaria = $model->executeSqlQuery($sql);

        $libretaSanitaria = self::formatData($libretaSanitaria, 'Problame al obtener carnet de manipulacion');
        return $libretaSanitaria;
    }
}
