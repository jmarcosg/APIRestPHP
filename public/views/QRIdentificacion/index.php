<?php
$noUrl = true;
include './config.php';

use App\Controllers\QRIdentificacion\QRI_CodigoQRController;
use App\Controllers\QRIdentificacion\QRI_PersonaController;

$qr = QRI_CodigoQRController::index(['qr_token' => $_GET['token']]);

if (count($qr) > 0) {
    $qr = $qr[0];
    $persona = QRI_PersonaController::index(['id' => $qr['id_persona_identificada']]);

    sendRes($persona);
} else {
    sendRes(null, "Persona no encontrada");
}
exit;
