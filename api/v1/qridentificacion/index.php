<?php

use App\Controllers\QRIdentificacion\QRI_CodigoQRController;
use App\Controllers\QRIdentificacion\QRI_PersonaController;
use App\Controllers\QRIdentificacion\QRI_UsuarioController;
use App\Traits\QRIdentificacion\RequestGenerarQR;
use App\Traits\QRIdentificacion\RequestGenerarVCard;

if ($url['method'] == "GET") {
    if (isset($_GET['action'])) {

        switch ($_GET['action']) {
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
                break;
            case '2':
                $data = QRI_UsuarioController::index(['email' => $_GET['email']]);

                echo var_dump($data);

                if (count($data) == 0) {
                    $data = [
                        'usuario' => null,
                        'error' => "Usuario no encontrado"
                    ];
                } else {
                    $data['error'] = null;
                }
                break;

            case '3':
                $data = QRI_PersonaController::index(['id' => $_GET['id']]);
                break;
        }
    } else {
        $data = [
            'qr' => null,
            'error' => "QR no encontrado"
        ];
    }

    echo json_encode($data);
}

if ($url['method'] == "POST") {
    switch ($_POST['action']) {
        case '1':
            QRI_PersonaController::store($_POST);
            $data = [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'cargo' => $_POST['cargo']
            ];
            $persona = QRI_PersonaController::index($data)[0];
            $usuario = QRI_UsuarioController::index(['email' => $_POST['mailUsuario']])[0];
            $qrs = QRI_CodigoQRController::index();

            $dataQR = [
                'id_usuario' => $usuario['id'],
                'id_persona_identificada' => $persona['id'],
                'qr_path' => 'E:/Dataserver/Replica/projects_files/qr-identificacion/' . (count($qrs) + 1) . "/",
                'qr_token' => md5($persona['email'] . $usuario['email'] . (count($qrs) + 1))
            ];

            if (QRI_CodigoQRController::store($dataQR)) {
                $dataQR['sessionkey'] = $_POST['sessionkey'];
                $dataQR['id_solicitud'] = count($qrs) + 1;
                $resp = RequestGenerarQR::sendRequest($dataQR);

                echo json_encode($resp);
            }
            break;

        case '2':
            QRI_UsuarioController::store($_POST);
            break;

        case '3':
            echo json_encode(RequestGenerarVCard::generateVcard($_POST));
            break;
    }
}
