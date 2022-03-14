<?php
die;
require_once './vendor/autoload.php';
define('PROD', $_ENV['PROD'] == 'true' ? true : false);
require_once './app/config/db.php';


/* Insercion masiva de datos para TOTEMS */

use App\Connections\BaseDatos;

function randomDate($start_date, $end_date)
{
    // Convert to timetamps
    $min = strtotime($start_date);
    $max = strtotime($end_date);

    // Generate random number using above bounds
    $val = rand($min, $max);

    // Convert back to desired date format
    return date('Y-m-d H:i:s', $val);
}

define('DB_NAME', 'info');
set_time_limit(5000);
$conn = new BaseDatos();
for ($i = 0; $i < 2000; $i++) {
    $day = randomDate('2022/02/10 00:00:00', '2022/02/10 23:59:59');
    $result = $conn->store('totemsGonza', [
        'totemID' => 6,
        'timeStamp' => $day,
        'plate' => 'AB-188-FS',
        'cityPlate' => 2
    ]);
}

require_once './app/config/db.php';

use App\Models\Acarreo;

$acarreo = new Acarreo();
$data = $acarreo->getByReferenciaId(12);
die;
