<?php
include './app/config/global.php';

use App\Controllers\Arbolado\Arb_SolicitudController;

$solicitudController =  new Arb_SolicitudController();
$solicitudController->getSolicitudPodaPdf(11, 'acta');
