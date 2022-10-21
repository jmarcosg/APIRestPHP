<?php

namespace App\Controllers\IdeasPropuestas;

use App\Models\IdeasPropuestas\IdeasPropuestas;

trait SqlTrait
{
    public static function getUserSql($usuario, $password)
    {
        $sql =
            "SELECT 
                id,
                nombre,
                legajo,
                dni,
                usuario,
                password,
                tipo,
                categoria,
                secretaria,
                subsecretaria,
                info,
                is_admin
            FROM dbo.ip_usuarios
            WHERE usuario = '$usuario' AND password = '$password'";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql);

        return $result;
    }

    public static function getContentSql($idUsuario)
    {
        $sql =
            "SELECT 
                content
            FROM dbo.ip_ideas
            WHERE id_usuario = '$idUsuario'";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }

    public static function getContentsSql()
    {
        $sql =
            "SELECT 
                ipi.id as id,
                ipi.content as content,
                ipu.nombre as nombre,
                ipu.dni as dni,
                ipu.legajo as legajo
            FROM ip_ideas ipi
            LEFT JOIN ip_usuarios ipu ON ipu.id = ipi.id_usuario";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }
}
