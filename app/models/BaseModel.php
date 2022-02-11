<?php

namespace App\Models;

use App\Connections\BaseDatos;

class BaseModel
{
    protected $table;

    public function save()
    {
        $array = json_decode(json_encode($this), true);
        $conn = new BaseDatos();
        $result = $conn->store($this->table, $array);

        /* Guardamos los errores */
        if ($conn->getError()) {
            $error =  $conn->getError() . ' | Error al guardar el usuario';
            cargarLogFile('store_user', $error, get_class(), __FUNCTION__);
        } else {
            $msg = "Usuario: $result creado correctamente.";
            cargarLogFile('store_user', $msg, get_class(), __FUNCTION__);
        }
        return $result;
    }

    public function list($param = [], $ops = [])
    {
        $class = get_class($this);
        $conn = new BaseDatos();
        $resource = $conn->search($this->table, $param, $ops);
        $usuarios = [];
        $error = $conn->getError();
        while ($row = odbc_fetch_array($resource)) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    public function get($params)
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $params);
        $usuario = $conn->fetch_assoc($result);

        /* Guardamos los errores */
        if ($conn->getError()) {
            $error =  $conn->getError() . ' | Error a obtener la solicitud: ' . $params['id'];
            cargarLogFile('get_user', $error, get_class(), __FUNCTION__);
        }
        return $usuario;
    }

    public function update($res, $id, $column)
    {
        $conn = new BaseDatos();
        $result = $conn->update($this->table, $res, $id, $column);

        /* Guardamos los errores */
        if ($conn->getError()) {
            $error =  $conn->getError() . ' | Error a modificar un formulario ' . $id;
            cargarLogFile('get_user', $error, get_class(), __FUNCTION__);
        }
        return $result;
    }
}
