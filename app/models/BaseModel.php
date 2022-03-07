<?php

namespace App\Models;

use ErrorException;

use App\Connections\BaseDatos;

class BaseModel
{
    protected $table;
    protected $softDeleted = false;

    public function list($param = [], $ops = [])
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $param, $ops);

        if (!$result instanceof ErrorException) {
            $data = [];
            while ($row = odbc_fetch_array($result)) $data[] = $row;
            return $data;
        } else {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
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
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }
        return $result;
    }

    public function save()
    {
        $array = json_decode(json_encode($this), true);
        $conn = new BaseDatos();
        $result = $conn->store($this->table, $array);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }
        return $result;
    }

    public function update($req, $id)
    {
        unset($req[$this->identity]);

        $conn = new BaseDatos();
        $result = $conn->update($this->table, $req, $id, $this->identity);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }

        return $result;
    }

    public function delete($id)
    {
        $conn = new BaseDatos();

        if (!$this->softDeleted) {
            /* Si el modelo no tiene el softdeleted, borramos el recurso completo de la DB */
            $result = $conn->delete($this->table, [$this->identity => $id]);
        } else {
            /* Si el modelo tiene el softdeleted, modificamos la columna que afecta al softdeleted */
            $deleted_at = date("Y-m-d H:i:s", time());
            $data = $this->get([$this->identity => $id]);
            if (!isset($data[$this->softDeleted]) && $data[$this->softDeleted] == null) {
                $result = $this->update([$this->softDeleted => $deleted_at], $id);
            } else {
                $result = new ErrorException('El recurso ya se encuentra eliminado');
            }
        }

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        }
        return $result;
    }

    public function executeSqlQuery(string $sql)
    {
        try {
            $conn = new BaseDatos();
            $query =  $conn->query($sql);
            $result = $conn->fetch_assoc($query);
            return $result;
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
