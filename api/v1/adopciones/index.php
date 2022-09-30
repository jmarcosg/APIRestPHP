<?php

use App\Controllers\Adopciones\Adop_AnimalesController;
use App\Controllers\Adopciones\Adop_VecinosController;
use App\Controllers\Adopciones\Adop_AdopcionesController;

use App\Traits\QRIdentificacion\RequestGenerarQR;
use App\Traits\QRIdentificacion\RequestGenerarVCard;

if ($url['method'] == "GET") {
	if (isset($_GET['action'])) {

		switch ($_GET['action']) {
			case '1':
				// Listado de animales
				$animalesController = new Adop_AnimalesController();
				$data = $animalesController->index();

				if (count($data) == 0) {
					$data = [
						'qr' => null,
						'error' => "QR no encontrado"
					];
				} else {
					$data['error'] = null;
				}
				break;
			case '2':
				// Listado de vecinos
				$data = Adop_VecinosController::index();

				echo var_dump($data);

				if (count($data) == 0) {
					$data = [
						'usuario' => null,
						'error' => "Vecino no encontrado"
					];
				} else {
					$data['error'] = null;
				}
				break;

			case '3':
				// Listado de adopciones
				$data = Adop_AdopcionesController::index();
				break;
		}
	} else {
		$data = [
			'adopcion' => null,
			'error' => "Adopcion no encontrada"
		];
	}

	echo json_encode($data);
}

if ($url['method'] == "POST") {
	switch ($_POST['action']) {
		case '1':
			// Crear animal
			Adop_AnimalesController::store($_POST);
			$data = [
				'nombre' => $_POST['nombre'],
				'edad' => $_POST['raza'],
				'tamanio' => $_POST['tamanio'],
				'castrado' => $_POST['castrado'],
				'descripcion' => $_POST['descripcion']
			];
			$persona = QRI_PersonaController::index($data)[0];
			$usuario = QRI_UsuarioController::index(['email' => $_POST['mailUsuario']])[0];
			$qrs = QRI_CodigoQRController::index();

			$dataQR = [
				'id_usuario' => $usuario['id'],
				'id_persona_identificada' => $persona['id'],
				'qr_path' => 'E:/Dataserver/Replica/projects_files/qr-identificacion/' . (count($qrs) + 1) . "/",
				'qr_token' => md5($persona['email'] . $usuario['email'] . (count($qrs) + 1))
			];

			if (QRI_CodigoQRController::store($dataQR)) {
				$dataQR['sessionkey'] = $_POST['sessionkey'];
				$dataQR['id_solicitud'] = count($qrs) + 1;
				$resp = RequestGenerarQR::sendRequest($dataQR);

				echo json_encode($resp);
			}
			break;

		case '2':
			QRI_UsuarioController::store($_POST);
			break;

		case '3':
			echo json_encode(RequestGenerarVCard::generateVcard($_POST));
			break;
	}
}
