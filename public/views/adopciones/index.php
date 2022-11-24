<?php
$noUrl = true;
include './config.php';

use App\Controllers\Adopciones\Adop_AnimalesController;

if ($_GET['action'] == 'anp') {
    $animalesController = new Adop_AnimalesController();
    $data = $animalesController->indexEverything();

    $data = [
        'data' => $data,
    ];

    if (count($data) == 0) {
        $data = [
            'animal' => null,
            'error' => "No hay registros de animales"
        ];
    } else {
        $data['error'] = null;
    }


    if (!$data instanceof ErrorException) {
        sendRes($data);
    } else {
        sendRes(null, "No se encuentra el registro buscado");
    }
}

exit;
