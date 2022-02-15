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

        if ($result instanceof ErrorException) cargarLogFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        return $result;
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

    public function update($req)
    {
        $id = $req[$this->identity];
        unset($req[$this->identity]);

        $conn = new BaseDatos();
        $result = $conn->update($this->table, $req, $id, $this->identity);

        if ($result instanceof ErrorException) cargarLogFileEE($this->logPath, $result, get_class($this), __FUNCTION__);

        return $result;
    }

    public function delete($params)
    {
        $conn = new BaseDatos();
        $result = $conn->delete($this->table, $params);

        if ($result instanceof ErrorException) cargarLogFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        return $result;
    }
}
