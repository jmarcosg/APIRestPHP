<?php
die();
define('PROD', $_ENV['PROD'] == 'true' ? true : false);
require_once './vendor/autoload.php';
require_once './app/config/db.php';

use App\Models\Acarreo;

$acarreo = new Acarreo();
$data = $acarreo->getByReferenciaId(12);
die;

