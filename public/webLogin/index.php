<?php
include './config.php';

use App\Controllers\Weblogin\LoginController;

$loginController = new LoginController();
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['user']) && isset($_POST['pass'])) {
    $userData = $loginController->getUserData($_POST['user'], $_POST['pass']);
    sendRes($userData);
}
exit;
