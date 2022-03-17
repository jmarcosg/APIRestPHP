<?php

require_once '../../app/config/global.php';

$token = getBearerToken();
if ($token == TOKEN_KEY) {
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
