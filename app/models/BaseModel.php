<?php

namespace App\Models;

use ErrorException;

use App\Connections\BaseDatos;

class BaseModel
{
    protected $table;
    protected $softDeleted = false;
    public $value;

    public function list($param = [], $ops = [])
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $param, $ops);

        if (!$result instanceof ErrorException) {
            $data = [];
            while ($row = odbc_fetch_array($result)) $data[] = $row;
            $this->value = $data;
            return $this;
        } else {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
            $this->value = $result;
        }
        return $this;
    }

    public function get($params)
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $params);

        if (!$result instanceof ErrorException) {
            $this->value = $conn->fetch_assoc($result);

            /* Ejecutamos los metodos para obtener las relaciones */
            $methods = $this->filterMethods(get_class_methods($this));
            foreach ($methods as $method) $this->$method();
        } else {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
            $this->value = $result;
        }
        return $this;
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

    /**
     * Genera relación de uno a uno     
     *  
     * @access public
     * @param string $class instancia de la clase en se requiere buscar.
     * @param string $source clave foranea del modelo $this.
     * @param string $destiny clave primaria de $class.
     * @return this
     */
    public function hasOne($class, $source, $destiny)
    {
        $instance = new $class();
        $data = $instance->get([$destiny => $this->value[$source]]);
        $this->value[$source] = $data->value;
        return $this;
    }

    public function executeSqlQuery(string $sql, $fetch_assoc = true)
    {
        try {
            $conn = new BaseDatos();
            $query =  $conn->query($sql);

            if ($fetch_assoc) {
                $result = $conn->fetch_assoc($query);
            } else {
                $result = [];
                while ($row = odbc_fetch_array($query)) {
                    $result[] = $row;
                }
            }
            return $result;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function filterMethods($methods)
    {
        /* Solamente los metodos de la clase hija */
        return array_values(array_filter($methods, function ($method) {
            return
                $method != "__construct" &&
                $method != "set" &&
                $method != "list" &&
                $method != "get" &&
                $method != "save" &&
                $method != "update" &&
                $method != "delete" &&
                $method != "hasOne" &&
                $method != "executeSqlQuery" &&
                $method != "filterMethods";
        }));
    }
}
