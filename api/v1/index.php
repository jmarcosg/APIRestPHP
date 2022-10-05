<?php


require_once '../../app/config/global.php';

use App\Controllers\Common\LoginController;

$token = getBearerToken();
/* s */
if (LoginController::isLogin($token)) {
	$file = "./" . $url['path'] . "/index.php";
	if (file_exists($file)) {
		include "./" . $url['path'] . "/index.php";
	} else {
		sendRes(null, 'No existe el endpoint.');
	}
} else {
	sendRes(null, 'No se encuentra autorizado');
	header("HTTP/1.1 401 Unauthorized");
}
session_unset();
