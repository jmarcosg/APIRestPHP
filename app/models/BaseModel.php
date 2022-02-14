<?php

namespace App\Models;

use ErrorException;

use App\Connections\BaseDatos;

class BaseModel
{
    protected $table;

    public function save()
    {
        $array = json_decode(json_encode($this), true);
        $conn = new BaseDatos();
        $result = $conn->store($this->table, $array);

        if (!$result instanceof ErrorException) {
            return $result;
        } else {
            cargarLogFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }
    }

    public function list($param = [], $ops = [])
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $param, $ops);

        if (!$result instanceof ErrorException) {
            $data = [];
            while ($row = odbc_fetch_array($result)) $data[] = $row;
            return $data;
        } else {
            cargarLogFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
            return $result;
        }
    }

    public function get($params)
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $params);

        if (!$result instanceof ErrorException) {
            $result = $conn->fetch_assoc($result);
        } else {
            cargarLogFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }

    public function update($res, $id, $column)
    {
        $conn = new BaseDatos();
        $result = $conn->update($this->table, $res, $id, $column);

        /* Guardamos los errores */
        /* if ($conn->getError()) {
            $error =  $conn->getError() . ' | Error a modificar un formulario ' . $id;
            cargarLogFileEE('get_user', $error, get_class(), __FUNCTION__);
        } */
        return $result;
    }
}
