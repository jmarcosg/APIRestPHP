<?php

namespace App\Controllers;

use App\Models\Empleado;

class EmpleadoController
{
    public function getByDocumentoAndGender($params)
    {
        $empleado = new Empleado();
        return $empleado->getByDocumentoAndGender($params['doc'], $params['sexo']);
    }
}
