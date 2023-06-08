<?php

include './config.php';

use App\Controllers\Weblogin\LoginController;

$loginController = new LoginController();
if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    LoginController::getUserData();
} else {
    sendRes(null, 'El método no es valido');
}
exit;
