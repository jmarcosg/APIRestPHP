<?php

require_once '../../app/config/global.php';

$token = getBearerToken();
switch ($url['path']) {
	case 'usuario':
		include './usuario/index.php';
		break;
}
