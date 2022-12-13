<?php

namespace App\Controllers\QRIdentificacion;

use App\Controllers\QRIdentificacion\QRI_CodigoQRController;
use App\Controllers\QRIdentificacion\QRI_UsuarioController;
use App\Traits\QRIdentificacion\RequestGenerarQR;
use App\Traits\QRIdentificacion\RequestGenerarVCard;
use App\Models\QRIdentificacion\QRI_Persona;
use ErrorException;

class QRI_PersonaController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'QRI_persona';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new QRI_Persona();
        $data = $data->list($param, $ops)->value;

        return $data;
        // $data = new QRI_Persona();
        // $data = $data->list($param, $ops)->value;

        // if (!$data instanceof ErrorException) {
        //     sendRes($data);
        // } else {
        //     sendRes(null, "Error");
        // }
        // exit;
    }

    public static function indexOf($param = [], $ops = [])
    {
        $data = new QRI_Persona();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($_PUT)
    {
        $persona = QRI_PersonaController::indexOf(['dni' => $_PUT['dni']]);

        $_PUT['deshabilitado'] = 0;
        if (count($persona) == 0) {
            $personaCargada = new QRI_Persona();
            $personaCargada->set($_PUT);
            $idPersona = $personaCargada->save();

            $persona = QRI_PersonaController::index(['id' => $idPersona])[0];

            $usuario = QRI_UsuarioController::index(['email' => $_PUT['mailUsuario']])[0];
            $cantQRs = count(QRI_CodigoQRController::index());

            $path = PROD ? "E:\Dataserver\Produccion\projects_files\qr-identificacion\\" : "E:\Dataserver\Replica\projects_files\qr-identificacion\\";

            $dataQR = [
                'id_usuario' => $usuario['id'],
                'id_persona_identificada' => $idPersona,
                'qr_path' => $path,
                'qr_token' => md5($persona['email'] . $usuario['email'] . $cantQRs)
            ];

            $dataQR['qr_local_path'] = "";

            if (ENV == "local") {
                $dataQR['qr_local_path'] = "C:/laragon/www/APIRestPHP/files/qr-identificacion/";
            }

            $idNuevoQR = QRI_CodigoQRController::store($dataQR);

            if (!$idNuevoQR instanceof ErrorException) {
                $dataQR['qr_path'] .= "$idNuevoQR/";
                $dataQR['sessionkey'] = $_PUT['sessionkey'];
                $dataQR['id_solicitud'] = $idNuevoQR;
                $resp = RequestGenerarQR::sendRequest($dataQR);

                $nuevoQR = QRI_CodigoQRController::index(['id' => $idNuevoQR])[0];
                $resp = ["error" => null, "message" => "exito", "token" => $nuevoQR['qr_token']];
            } else {
                $resp = ["error" => "hubo un error en la creacion del QR", "message" => null];
            }
        } else {
            unset($_PUT['mailUsuario']);
            unset($_PUT['sessionkey']);
            $resp = self::update($_PUT, $persona[0]['id']);
            $qr = QRI_CodigoQRController::index(['id_persona_identificada' => $persona[0]['id']]);

            if ($resp instanceof ErrorException) {
                $resp = ["error" => "Hubo un error en la actualizacion del QR", "message" => null];
            } else {
                $resp = ["error" => null, "message" => "exito", "token" => $qr[0]['qr_token']];
            }
        }
        return $resp;
    }

    public static function update($data, $id)
    {
        $persona = new QRI_Persona();
        return $persona->update($data, $id);
    }
}
