<?php

use App\Controllers\CredencialesEmpleados\CREDEMP_CodigoQRController;
use App\Controllers\CredencialesEmpleados\CREDEMP_TemplateController;
use App\Controllers\CredencialesEmpleados\CREDEMP_InputController;
use App\Controllers\CredencialesEmpleados\CREDEMP_PersonaController;
use App\Controllers\CredencialesEmpleados\CREDEMP_TipoController;
use App\Controllers\CredencialesEmpleados\CREDEMP_UsuarioController;
use App\Controllers\CredencialesEmpleados\CREDEMP_ValorController;
use App\Traits\CredencialesEmpleados\PersonaConBase64;

$dotenv = \Dotenv\Dotenv::createImmutable(('./credencialesempleados'));
$dotenv->load();

include './credencialesempleados/config.php';

if ($url['method'] == 'GET') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        unset($_GET['action']);

        switch ($action) {
            case '1':
                //GET TEMPLATES E INPUTS
                $data = [];
                $templates = CREDEMP_TemplateController::index(['deshabilitado' => 0]);
                if (!empty($templates)) {
                    $tempData = [];
                    foreach ($templates as $template) {
                        $tempInputs = CREDEMP_InputController::index(['id_template' => $template['id']]);
                        $inputs = [];
                        foreach ($tempInputs as $input) {

                            $tempType = CREDEMP_TipoController::index(['id' => $input['id_tipo']])[0];

                            $input['tipo'] = $tempType;
                            array_push($inputs, $input);
                        }
                        $template['inputs'] = $inputs;
                        array_push($tempData, $template);
                        array_push($data, $tempData);
                        $tempData = [];
                    }
                }
                break;
            case '2':
                $data = CREDEMP_UsuarioController::index(['email' => $_GET['email']]);

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
                $data = CREDEMP_TipoController::index();
                break;

            case '4':
                $personas = CREDEMP_PersonaController::index(['deshabilitado' => 0]);
                $data = [];
                foreach ($personas as $persona) {
                    $tempData = [];
                    $values = CREDEMP_ValorController::index(['id_persona' => $persona['id'], 'id_template' => $persona['id_template']]);
                    $inputs = CREDEMP_InputController::index(['id_template' => $persona['id_template']]);
                    $template = CREDEMP_TemplateController::index(['id' => $persona['id_template']])[0];
                    $inputsAndTypes = [];
                    foreach ($inputs as $input) {
                        $tipo = CREDEMP_TipoController::index(['id' => $input['id_tipo']])[0];
                        $value = CREDEMP_ValorController::index(['id_input' => $input['id'], 'id_persona' => $persona['id'], 'id_template' => $template['id']])[0];
                        $qr = CREDEMP_CodigoQRController::index(['id_persona_identificada' => $persona['id']])[0];
                        $persona['qr'] = $qr;
                        $input['tipo'] = $tipo;
                        $input['value'] = $value;
                        $inputsAndTypes[] = $input;
                    }
                    $template['inputs'] = $inputsAndTypes;
                    $persona['template'] = $template;
                    $tempData[] = $persona;
                    $data[] = $tempData;
                }
                break;

            case '5':
                $data = PersonaConBase64::devolverPersonaConBase64($_GET);
                break;

            case '6':
                $data = CREDEMP_PersonaController::index(['dni' => $_GET['dni']])[0];
                $template = CREDEMP_TemplateController::index(['id' => $data['id_template']])[0];
                $inputs = CREDEMP_InputController::index(['id_template' => $data['id_template']]);
                $inputsAndTypes = [];
                foreach ($inputs as $input) {
                    $tipo = CREDEMP_TipoController::index(['id' => $input['id_tipo']])[0];
                    $value = CREDEMP_ValorController::index(['id_input' => $input['id'], 'id_persona' => $data['id'], 'id_template' => $template['id']])[0];
                    $input['tipo'] = $tipo;
                    $input['value'] = $value;
                    $inputsAndTypes[] = $input;
                }
                $template['inputs'] = $inputsAndTypes;
                $data['template'] = $template;
                break;

            case '7':
                $templates = CREDEMP_TemplateController::index();
                $data = [];
                foreach ($templates as $template) {
                    $inputs = CREDEMP_InputController::index(['id_template' => $template['id']]);
                    $inputsAndTypes = [];
                    foreach ($inputs as $input) {
                        $tipo = CREDEMP_TipoController::index(['id' => $input['id_tipo']])[0];
                        $input['tipo'] = $tipo;
                        $inputsAndTypes[] = $input;
                    }
                    $template['inputs'] = $inputsAndTypes;
                    $data[] = $template;
                }
                break;

            case '8':
                $data = CREDEMP_TemplateController::index(['id' => $_GET['id']])[0];
                $inputs = CREDEMP_InputController::index(['id_template' => $data['id']]);
                $personas = CREDEMP_PersonaController::index(['id_template' => $_GET['id']]);
                $data['hasData'] = count($personas) > 0;

                $inputsAndTypes = [];
                foreach ($inputs as $input) {
                    $tipo = CREDEMP_TipoController::index(['id' => $input['id_tipo']])[0];
                    $input['tipo'] = $tipo;
                    $inputsAndTypes[] = $input;
                }
                $data['inputs'] = $inputsAndTypes;
                break;
        }
        if (!$data instanceof ErrorException) {
            sendRes($data);
            exit;
        } else {
            sendRes(null, "Ocurrio un error");
            exit;
        }
    }
}

if ($url['method'] == "PUT") {
    $_PUT = json_decode(file_get_contents('php://input'), true);
    // parse_str(file_get_contents('php://input'), $_PUT);
    $action = $_PUT['action'];
    unset($_PUT['action']);

    switch ($action) {
        case '1':
            $resp = CREDEMP_UsuarioController::store($_PUT);
            break;

        case '2':
            $resp = CREDEMP_TemplateController::store($_PUT);
            break;

        case '3':
            $resp = CREDEMP_PersonaController::store($_PUT);
            break;

        case '4':
            $resp = CREDEMP_PersonaController::update(['deshabilitado' => 1], $_PUT['id']);
            break;

        case '5':
            $resp = CREDEMP_TemplateController::update(['deshabilitado' => 1], $_PUT['id']);
            break;

        case '6':
            $resp = CREDEMP_TemplateController::update(['deshabilitado' => 0], $_PUT['id']);
            break;
    }

    if (!$resp instanceof ErrorException) {
        sendRes($resp);
    } else {
        sendRes(null, $resp);
    }
}
