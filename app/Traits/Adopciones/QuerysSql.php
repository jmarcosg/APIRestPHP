<?php

namespace App\Traits\Adopciones;

trait QuerysSql
{
    public static function getMostRecentAdopterAdoptions($idAdoptante)
    {
        $sql =
            "SELECT id, id_adoptante, id_animal, fecha_adopcion 
            FROM dbo.ADOP_adopciones AS adopciones
            WHERE id_adoptante = " . $idAdoptante . " AND (fecha_adopcion = (SELECT TOP 1 fecha_adopcion
            FROM dbo.ADOP_adopciones AS adopciones2 WHERE adopciones.id_animal = adopciones2.id_animal ORDER BY fecha_adopcion DESC))";

        return $sql;
    }
}
