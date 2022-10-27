<?php

use App\Controllers\QRIdentificacion\QRI_CodigoQRController;
use App\Controllers\QRIdentificacion\QRI_PersonaController;
use App\Controllers\QRIdentificacion\QRI_UsuarioController;
use App\Traits\QRIdentificacion\RequestGenerarQR;
use App\Traits\QRIdentificacion\RequestGenerarVCard;

if ($url['method'] == "GET") {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        unset($_GET['action']);

        switch ($action) {
            case '1':
                $qrController = new QRI_CodigoQRController();
                $data = $qrController->index(['qr_token' => $_GET['token']]);

                if (count($data) == 0) {
                    $data = [
                        'qr' => null,
                        'error' => "QR no encontrado"
                    ];
                } else {
                    $data['error'] = null;
                }
                exit;
            case '2':
                $data = QRI_UsuarioController::index(['email' => $_GET['email']]);

                if (count($data) == 0) {
                    $data = [
                        'usuario' => null,
                        'error' => "Usuario no encontrado"
                    ];
                } else {
                    $data = $data[0];
                    $data['error'] = null;
                }
                break;

            case '3':
                $data = QRI_PersonaController::index(['id' => $_GET['id']]);
                break;

            case '4':
                $data = QRI_CodigoQRController::index(['qr_token' => $_GET['token']]);
                break;

            case '5':
                $data = QRI_PersonaController::index();
                break;

            case '6':
                $data = QRI_CodigoQRController::index(['id_persona_identificada' => $_GET['id']]);
                if (count($data) > 0) {
                    $data = $data[0];
                    $data['error'] = null;
                } else {
                    $data = ['error' => "Persona no encontrada", 'persona' => null];
                }
                break;
        }
    } else {
        $data = [
            'qr' => null,
            'error' => "QR no encontrado"
        ];
    }

    if (!$data instanceof ErrorException) {
        sendRes($data);
    } else {
        sendRes(null, "No se encuentra el registro buscado");
    }
    exit;
}

if ($url['method'] == "PUT") {
    $_PUT = json_decode(file_get_contents('php://input'), true);
    // parse_str(file_get_contents('php://input'), $_PUT);
    $action = $_PUT['action'];
    unset($_PUT['action']);

    switch ($action) {
        case '1':
            $resp = QRI_PersonaController::store($_PUT);
            break;

        case '2':
            $resp = QRI_UsuarioController::store($_PUT);
            exit;

        case '3':
            $resp = RequestGenerarVCard::generateVcard($_PUT);
            exit;
    }

    if (!$resp instanceof ErrorException) {
        sendRes($resp);
    } else {
        sendRes(null, $resp);
    }
}
