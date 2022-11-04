<?php
$noUrl = true;
include './config.php';

use App\Controllers\Adopciones\Adop_AnimalesController;

$animalesController = new Adop_AnimalesController();
$data = $animalesController->index();

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

exit;
