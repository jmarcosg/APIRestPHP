<?php

require_once '../../app/config/global.php';

$token = getBearerToken();
switch ($url['path']) {
	case 'wapusuario':
		include './wapusuario/index.php';
		break;
	case 'wappersona':
		include './wappersona/index.php';
		break;
	default:
		sendRes(null, 'no existe el endpoint', null);
		break;
}
