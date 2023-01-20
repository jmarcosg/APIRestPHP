<?php

namespace App\Controllers\JuegoFiestaConfluencia;

use App\Connections\BaseDatos;
use App\Models\JuegoFiestaConfluencia\MEMCONF_Partida;
use App\Traits\JuegoFiestaConfluencia\QuerysSql;

class MEMCONF_PartidaController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'MEMCONF_Partida';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new MEMCONF_Partida();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public function get($params)
    {
        $data = new MEMCONF_Partida();
        $data = $data->get($params)->value;
        return $data;
    }

    public static function getUserIfUserHasPlayedToday($idUsuario, $fechaHoy)
    {
        $memconf_partida = new MEMCONF_Partida();
        $sql = QuerysSql::getUserIfUserHasPlayedToday($idUsuario, $fechaHoy);
        $data = $memconf_partida->executeSqlQuery($sql, false);

        return $data;
    }

    public static function getGamesWon($fechaSeleccionada)
    {
        $memconf_partida = new MEMCONF_Partida();
        $sql = QuerysSql::getGamesWon($fechaSeleccionada);
        $data = $memconf_partida->executeSqlQuery($sql, false);

        return $data;
    }

    public static function store($res)
    {
        $data = new MEMCONF_Partida();
        $data->set($res);
        return $data->save();
    }

    public static function update($req, $id)
    {
        $data = new MEMCONF_Partida();
        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new MEMCONF_Partida();
        return $data->delete($id);
    }
}
