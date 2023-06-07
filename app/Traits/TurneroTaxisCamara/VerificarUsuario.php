<?php

namespace App\Traits\TurneroTaxisCamara;

trait VerificarUsuario
{
    public static function verificarUsuario($dni)
    {
        $taxistas = ["41591884", "37943865"];
        $valido = in_array($dni, $taxistas);
        return ["success" => $valido, "error" => $valido ? null : "El DNI no pertenece a un taxista"];
    }
}
