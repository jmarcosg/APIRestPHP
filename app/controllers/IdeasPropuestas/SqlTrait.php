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

    public static function getContentsSql($where)
    {
        $sql =
            "SELECT 
                ipi.id as id,
                ipi.content as content,
                ipu.nombre as nombre,
                ipu.dni as dni,
                ipu.legajo as legajo
            FROM ip_ideas ipi
            LEFT JOIN ip_usuarios ipu ON ipu.id = ipi.id_usuario WHERE $where";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }

    public static function getContentsSqlByUser()
    {
        $sql =
            "SELECT 
                ipu.dni as dni,
                ipu.nombre as nombre,
                count(ipi.id) as cantidad
            FROM ip_usuarios ipu
            RIGHT JOIN ip_ideas ipi ON ipi.id_usuario = ipu.id 
            GROUP BY ipu.nombre, ipu.dni";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }
}
