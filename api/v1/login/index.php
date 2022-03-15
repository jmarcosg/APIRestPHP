<?php

use App\Controllers\LoginController;

$loginController = new LoginController();

/* Metodo GET */
if ($token == TOKEN_KEY && $url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0) {
		$user = $loginController->getUserData($_GET);
		if (!$user instanceof ErrorException) {
			if ($user) {
				sendRes($user);
			} else {
				sendRes(null, 'No se encontro el usuario', $_GET);
			}
		} else {
			sendRes(null, $user->getMessage(), $_GET);
		};
	} else {
		header("HTTP/1.1 200 Bad Request");
	}
	eClean();
}

if ($token != TOKEN_KEY) {
	header("HTTP/1.1 401 Unauthorized");
} else {
	header("HTTP/1.1 200 Bad Request");
}
eClean();
