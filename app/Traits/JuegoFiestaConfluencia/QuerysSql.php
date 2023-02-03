<?php

namespace App\Traits\JuegoFiestaConfluencia;

trait QuerysSql
{
    public static function getUserIfUserHasPlayedToday($idUsuario, $fechaHoy)
    {
        $sql =
            "SELECT id, id_usuario, id_configuracion, aciertos, movimientos_totales, gano, fecha_jugada 
            FROM dbo.MEMCONF_partida
            WHERE id_usuario = $idUsuario AND fecha_jugada LIKE '%$fechaHoy%'";

        return $sql;
    }

    public static function getGames()
    {
        $sql =
            "SELECT dbo.MEMCONF_partida.id, 
            dbo.MEMCONF_partida.id_usuario, 
            dbo.MEMCONF_partida.id_configuracion, 
            dbo.MEMCONF_partida.aciertos, 
            dbo.MEMCONF_partida.movimientos_totales, 
            dbo.MEMCONF_partida.gano, 
            dbo.MEMCONF_partida.fecha_jugada, 
            dbo.MEMCONF_usuario.usuario_instagram 
            FROM dbo.MEMCONF_partida
            LEFT JOIN dbo.MEMCONF_usuario
            ON dbo.MEMCONF_partida.id_usuario = dbo.MEMCONF_usuario.id";

        return $sql;
    }

    public static function getGamesWon($fechaSeleccionada)
    {
        $sql =
            "SELECT dbo.MEMCONF_partida.id, 
            dbo.MEMCONF_partida.id_usuario, 
            dbo.MEMCONF_partida.id_configuracion, 
            dbo.MEMCONF_partida.aciertos, 
            dbo.MEMCONF_partida.movimientos_totales, 
            dbo.MEMCONF_partida.gano, 
            dbo.MEMCONF_partida.fecha_jugada, 
            dbo.MEMCONF_usuario.usuario_instagram 
            FROM dbo.MEMCONF_partida
            LEFT JOIN dbo.MEMCONF_usuario
            ON dbo.MEMCONF_partida.id_usuario = dbo.MEMCONF_usuario.id
            WHERE dbo.MEMCONF_partida.gano = 1 AND dbo.MEMCONF_partida.fecha_jugada LIKE '%$fechaSeleccionada%'";

        return $sql;
    }

    public static function getGiveawayWinners($cantidadGanadores, $fechaSeleccionada)
    {

        $sql =
            "SELECT TOP $cantidadGanadores 
            dbo.MEMCONF_partida.id, 
            dbo.MEMCONF_partida.id_usuario, 
            dbo.MEMCONF_partida.id_configuracion, 
            dbo.MEMCONF_partida.aciertos, 
            dbo.MEMCONF_partida.movimientos_totales, 
            dbo.MEMCONF_partida.gano, 
            dbo.MEMCONF_partida.fecha_jugada, 
            dbo.MEMCONF_usuario.usuario_instagram 
            FROM dbo.MEMCONF_partida
            LEFT JOIN dbo.MEMCONF_usuario
            ON dbo.MEMCONF_partida.id_usuario = dbo.MEMCONF_usuario.id
            WHERE dbo.MEMCONF_partida.gano = 1 AND dbo.MEMCONF_partida.fecha_jugada LIKE '%$fechaSeleccionada%'
            ORDER BY NEWID()";

        return $sql;
    }
}
