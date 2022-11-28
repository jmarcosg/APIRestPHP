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

    public static function getContentSql($where)
    {
        $sql =
            "SELECT 
                ipi.id as id,
                ipi.content as content,
                cat.nombre as categoria,
                cat.id as id_categoria
            FROM dbo.ip_ideas ipi
            LEFT JOIN ip_categorias cat ON cat.id = ipi.id_categoria 
            WHERE $where";

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
                
                CASE
                    WHEN ipi.id_usuario IS NOT NULL      
                    THEN ipu.nombre    
                    ELSE wPer.Nombre       
                END as nombre,
                
                CASE
                    WHEN ipi.id_usuario IS NOT NULL      
                    THEN ipu.dni     
                    ELSE wPer.Documento       
                END as dni,
                
                ipu.legajo as legajo,
                
                cat.nombre as categoria
            FROM ip_ideas ipi
                LEFT JOIN ip_categorias cat ON cat.id = ipi.id_categoria 
                LEFT JOIN ip_usuarios ipu ON ipu.id = ipi.id_usuario
                LEFT JOIN wapUsuarios wUsr ON wUsr.ReferenciaID = ipi.id_usuario_wl 
                LEFT JOIN wapPersonas wPer ON wPer.ReferenciaID = wUsr.PersonaID 
            WHERE $where";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }

    public static function getContentsSqlByUser($tabla = 'interno')
    {
        if ($tabla == 'interno') {
            $sql =
                "SELECT 
                ipu.dni as dni,
                ipu.nombre as nombre,
                count(ipi.id) as cantidad
            FROM ip_usuarios ipu
            INNER JOIN ip_ideas ipi ON ipi.id_usuario = ipu.id 
            GROUP BY ipu.nombre, ipu.dni";
        }

        if ($tabla == 'externo') {
            $sql =
                "SELECT 
                wapper.Documento as dni,
                wapper.Nombre as nombre,
                count(ipi.id) as cantidad
            FROM wapUsuarios as wapUsr
            INNER JOIN ip_ideas ipi ON ipi.id_usuario_wl = wapUsr.ReferenciaID 
            INNER JOIN wapPersonas wapper ON wapper.ReferenciaID = wapUsr.PersonaID 
            GROUP BY wapper.Nombre, wapper.Documento";
        }

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }

    public static function getContentsSqlByCat()
    {
        $sql =
            "SELECT 
                cat.nombre as nombre,
                count(ipi.id) as cantidad
            FROM ip_categorias cat
            INNER JOIN ip_ideas ipi ON ipi.id_categoria = cat.id 
            GROUP BY cat.nombre";

        $model = new IdeasPropuestas();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }
}
