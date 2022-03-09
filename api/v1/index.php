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
	case 'deportesusuario':
		include './deportesusuario/index.php';
		break;
	case 'acarreo':
		include './acarreo/index.php';
		break;
	case 'libretasanitaria':
		include './libretasanitaria/index.php';
		break;
	case 'empleado':
		include './empleado/index.php';
		break;
	case 'login':
		include './login/index.php';
		break;
	case 'licenciaconducir':
		include './licenciaconducir/index.php';
		break;
	case 'totemsdata':
		include './totemsdata/index.php';
		break;
	default:
		sendRes(null, 'no existe el endpoint', null);
		break;
}
