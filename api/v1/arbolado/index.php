<?php

use App\Controllers\Arbolado\Arb_SolicitudController;

/* Metodo GET */

if ($url['method'] == 'GET') {
	if (isset($_GET) && count($_GET) > 0 && isset($_GET['action'])) {
		$action = $_GET['action'];
		unset($_GET['action']);

		switch ($action) {
			case '0':
				/* Obtenemos todas las solicitudes de poda */
				Arb_SolicitudController::index("1=1");
				break;

			case '1':
				/* Obtenemos todas las solicitudes de poda nuevas */
				Arb_SolicitudController::index("estado = 'nuevo'");
				break;

			case '2':
				/* Obtenemos todas las solicitudes de poda aprobadas */
				Arb_SolicitudController::index("estado = 'aprobado'");
				break;

			case '3':
				/* Obtenemos todas las solicitudes de poda rechazadas */
				Arb_SolicitudController::index("estado = 'rechazado'");
				break;

			case '4':
				/* Obtenemos todas las solicitudes de poda del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("id_usuario = $id_usuario");
				break;

			case '5':
				/* Obtenemos todas las solicitudes de poda nuevas  del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("id_usuario = $id_usuario AND estado = 'nuevo'");
				break;

			case '6':
				/* Obtenemos todas las solicitudes de poda aprobadas  del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("id_usuario = $id_usuario AND estado = 'aprobado'");
				break;

			case '7':
				/* Obtenemos todas las solicitudes de poda rechazadas  del usuario */
				$id_usuario = $_GET['id_usuario'];
				Arb_SolicitudController::index("estado = 'rechazado'");
				break;

			case '8':
				Arb_SolicitudController::getById($_GET);
				break;

			default:
				$error = new ErrorException('El action no es valido');
				sendRes(null, $error->getMessage(), $_GET);
				exit;
		}
	}
}

/* Metodo POST */
if ($url['method'] == 'POST') {
	$action = $_POST['action'];
	unset($_POST['action']);

	switch ($action) {
		case '0':
			/* Guardamos una solicitud de poda */
			Arb_SolicitudController::store($_POST);
			break;

		default:
			$error = new ErrorException('El action no es valido');
			sendRes(null, $error->getMessage(), $_GET);
			exit;
	}
}

/* Metodo PUT */
if ($url['method'] == 'PUT') {
	parse_str(file_get_contents('php://input'), $_PUT);
	$id = $url['id'];

	/* Extraemos el contacto y el email  */
	$contacto = $_PUT['contacto'];
	$email = $_PUT['email'];
	unset($_PUT['contacto']);
	unset($_PUT['email']);

	$arbolado = $arbSolicitudController->update($_PUT, $id);

	if (!$arbolado instanceof ErrorException) {
		/* Enviamos el correo electronico */
		$data = [
			'id' => $id,
			'email' => $email,
			'contacto' => $contacto,
			'observacion' => $_PUT['observacion']
		];


		$arbSolicitudController->sendEmail($id, $_PUT['estado'], $data);
		$_PUT['id'] = $id;
		sendRes($_PUT);
	} else {
		sendRes(null, $arbolado->getMessage(), ['ReferenciaID' => $id]);
	};
	eClean();
}

/* Metodo DELETE */
if ($url['method'] == 'DELETE') {
	Arb_SolicitudController::delete($url['id']);
}

header("HTTP/1.1 200 Bad Request");

eClean();
