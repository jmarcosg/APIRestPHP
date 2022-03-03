<?php

namespace App\Controllers;

use App\Models\Login;
use App\Models\Empleado;

use ErrorException;

class LoginController
{
    public function getUserData($params)
    {
        $user = new Login();
        $userData = $user->getUserData($params['user'], $params['pass']);

        if ($userData instanceof ErrorException) return $userData;

        if ($userData && $userData->value && !$userData->error) {
            $data = [
                'authenticationToken' => $userData->value->authenticationToken,
                'profile' => $userData->value->profile,
                'apps' => $userData->value->apps
            ];

            /* Obtenemos los datos del legajo, si es que tiene */
            $emp = new Empleado();
            $genero = $userData->value->profile->genero->textID;
            $dni = $userData->value->profile->documento;
            $empData = $emp->getByDocumentoAndGender($dni, $genero);

            if ($empData && !$empData instanceof ErrorException) {
                $data['legajo'] = $empData;
            } else {
                $data['legajo'] = null;
            }

            return $data;
        } else {
            return new ErrorException($userData->error);
        }
    }
}
