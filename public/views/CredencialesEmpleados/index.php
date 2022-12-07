<?php

$noUrl = true;
include "../../../app/config/paths.php";

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'app/utils/funciones.php';

$dotenv = \Dotenv\Dotenv::createImmutable(V1_PATH . "/credencialesempleados");
$dotenv->load();

include './config.php';

use App\Controllers\CredencialesEmpleados\CREDEMP_CodigoQRController;
use App\Controllers\CredencialesEmpleados\CREDEMP_InputController;
use App\Controllers\CredencialesEmpleados\CREDEMP_PersonaController;
use App\Controllers\CredencialesEmpleados\CREDEMP_TemplateController;
use App\Controllers\CredencialesEmpleados\CREDEMP_ValorController;
use App\Models\Renaper;

$qr = CREDEMP_CodigoQRController::index(['qr_token' => $_GET['token']]);

if (count($qr) > 0) {
    $qr = $qr[0];
    $persona = CREDEMP_PersonaController::index(['id' => $qr['id_persona_identificada']])[0];

    $template = CREDEMP_TemplateController::index(['id' => $persona['id_template']])[0];

    $inputs = CREDEMP_InputController::index(['id_template' => $template['id']]);

    $inputsWithValues = [];
    foreach ($inputs as $input) {
        $value = CREDEMP_ValorController::index(['id_input' => $input['id'], 'id_persona' => $persona['id'], 'id_template' => $template['id']])[0];
        $input['value'] = $value;
        $inputsWithValues[] = $input;
    }
    $template['inputs'] = $inputsWithValues;
    $persona['template'] = $template;
    $renaper = new Renaper();
    $personImg = $renaper->getData($persona['genero'], $persona['dni']);
    $persona['img'] = $personImg->imagen;

    sendRes($persona);
} else {
    sendRes(null, "Persona no encontrada");
}
exit;
