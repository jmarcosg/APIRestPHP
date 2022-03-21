<?php

namespace App\Controllers;

use App\Models\Login;

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

            $dni = $userData->value->profile->documento;

            $referenciaId = $userData->value->profile->wapUsuarioID;
            $data['fetch'] = $this->viewFetch($referenciaId, $dni);

            return $data;
        } else {
            return new ErrorException($userData->error);
        }
    }

    public function viewFetch($referenciaId, $dni)
    {
        /** Determina que vamos a llamar desde el front */
        $user = new Login();
        $data = $user->viewFetch($referenciaId, $dni);

        $data['legajo'] = $data['legajo'] != null ? true : false;
        $data['libreta'] = $data['libreta'] != null ? true : false;
        $data['licencia'] = $data['licencia'] == null || $data['licencia'] == -1 ? false : true;
        $data['acarreo'] = $data['acarreo'] != null ? true : false;

        return $data;
    }
}
