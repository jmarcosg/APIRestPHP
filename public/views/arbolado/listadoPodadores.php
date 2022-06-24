<?php

include '../../../app/config/global.php';

use App\Models\Arbolado\MYPDF;
use App\Controllers\Arbolado\Arb_PodadorController;

$podadoresControllers =  new Arb_PodadorController();
$podadoresControllers->getPodadoresPdf();


