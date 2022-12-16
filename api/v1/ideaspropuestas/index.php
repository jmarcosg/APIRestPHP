<?php

use App\Controllers\IdeasPropuestas\IdeasPropuestasController;

$dotenv = \Dotenv\Dotenv::createImmutable('./ideaspropuestas/');
$dotenv->load();

include './ideaspropuestas/config.php';

if ($url['method'] == 'GET') {
	$action = $_GET['action'];
	unset($_GET['action']);

	switch ($action) {

		case 'getAllContents':
			IdeasPropuestasController::getContents();

		case 'getContentsWl':
			IdeasPropuestasController::getContents('id_usuario_wl =' . $_GET['id_usuario']);

		case 'getAllContentsUser':
			IdeasPropuestasController::getContentsByUser();

		case 'getAllContentsCategorias':
			IdeasPropuestasController::getCountContentsByCat();

		case 'getAllContentsByDni':
			IdeasPropuestasController::getContents('dni = ' . $_GET['dni']);

		case 'getUsuarios':
			IdeasPropuestasController::getUsuarios();

		case 'getCategorias':
			IdeasPropuestasController::getCategorias();

		case 'getContentsByCat':
			IdeasPropuestasController::getContentsByCat();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	switch ($action) {
		case 'login':
			IdeasPropuestasController::login();

		case 'saveContent':
			IdeasPropuestasController::saveContent();

		case 'editContent':
			IdeasPropuestasController::saveEditContent();

		case 'saveUser':
			IdeasPropuestasController::saveUser();

		case 'deleteContent':
			IdeasPropuestasController::deleteContent();

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
			break;
	}
}

header("HTTP/1.1 200 Bad Request");

eClean();
