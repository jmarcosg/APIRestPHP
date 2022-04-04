<?php

use App\Controllers\TributoController;

$tributoController = new TributoController();

/* Metodo GET */
if ($url['method'] == 'POST' && $_POST['type'] = 'saveStats') {
	$tributo = $tributoController->save($_POST);
}

header("HTTP/1.1 200 Bad Request");

eClean();
