<?php
include './config.php';

use App\Controllers\Weblogin\WlFotoPerfilController;

$token = getBearerToken();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    if ($token == TOKEN) {
        WlFotoPerfilController::saveFoto();
    } else {
        sendRes(null, 'No se encuentra autorizado');
    }
} else {
    sendRes(null, 'El método utilizado no es valido');
}
exit;
