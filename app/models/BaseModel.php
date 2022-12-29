<?php

namespace App\Models;

use ErrorException;

use App\Connections\BaseDatos;

class BaseModel
{
    /** Nombre de la tabla en el cual va operar el modelo */
    protected $table;

    /** Indica el nombre de la clave primaria del modelo */
    protected $identity;

    /** Indica si es un borrado logico o fisico */
    protected $softDeleted = false;

    protected $logPath;

    /** Valor que retorna luego de hacer la consulta */
    public $value;

    /** Campos de la tabla que se permiten en el modelo */
    protected $fillable = [];

    /** Arreglo generado para hacer el save en la base de datos */
    public $req;

    /** Ruta donde se almacena los archivos del proyecto */
    protected $filesUrl;

    /** Metodos que se deben volver a ejecutar en un metodo de tipo list */
    protected $reExectMethods = [];

    /** Metodos que no se deben ejecutar, en los en las relaciones */
    protected $filterMethod = [];

    /** Arreglo de indices, se utiliza para indicar si se inserta valor repetido */
    public $uniques = [];

    public function __construct()
    {
        $this->filterMethod = get_class_methods(get_parent_class($this));
    }

    public function set(array $req)
    {
        foreach ($this->fillable as $fill) {
            $this->req[$fill] = array_key_exists($fill, $req) ? $req[$fill] : null;
        }
    }

    public function list($param = [], $ops = [])
    {
        $conn = new BaseDatos();

        /* Consultamos si tiene el softDelete para realizar el filtrado */
        if (isset($this->softDeleted) && $this->softDeleted != null) {
            $param[$this->softDeleted] = null;
        }

        $result = $conn->search($this->table, $param, $ops);

        if (!$result instanceof ErrorException) {
            $data = [];
            while ($row = odbc_fetch_array($result)) $data[] = $row;
            $this->value = $data;
        } else {
            /* logFileEE($this->logPath, $result, get_class($this), __FUNCTION__); */
            createJsonError($this->logPath, $result, get_class($this), __FUNCTION__, $param, $this);
            $this->value = $result;
        }
        return $this;
    }

    public function get($params, $ops = [])
    {
        $conn = new BaseDatos();
        $result = $conn->search($this->table, $params, $ops);

        if (!$result instanceof ErrorException) {
            $this->value = $conn->fetch_assoc($result);
        } else {
            /* logFileEE($this->logPath, $result, get_class($this), __FUNCTION__); */
            createJsonError($this->logPath, $result, get_class($this), __FUNCTION__, $params, $this);
            $this->value = $result;
        }
        return $this;
    }

    public function save()
    {
        /* Ver el motivo de usar esto, supongo que es pos si entra la instancia de un modelo */
        $array = json_decode(json_encode($this->req), true);
        $conn = new BaseDatos();
        $result = $conn->store($this->table, $array);

        if ($result instanceof ErrorException) {
            /* logFileEE($this->logPath, $result, get_class($this), __FUNCTION__, $array); */
            createJsonError($this->logPath, $result, get_class($this), __FUNCTION__, $array, $this);
        }
        return $result;
    }

    public function update($req, $id)
    {
        unset($req[$this->identity]);

        $req = $this->formatPost($req);
        /* Verificamos si el recurso existe */
        $search = $this->get([$this->identity => $id])->value;
        if ($search) {
            $conn = new BaseDatos();
            $result = $conn->update($this->table, $req, $id, $this->identity);

            if ($result instanceof ErrorException) {
                /* logFileEE($this->logPath, $result, get_class($this), __FUNCTION__); */
                createJsonError($this->logPath, $result, get_class($this), __FUNCTION__, $req, $this);
            }
        } else {
            $result = new ErrorException('No se encuentra el recurso');
        }
        return $result;
    }

    public function delete($id)
    {
        /* Verificamos si el recurso existe */
        $result = null;
        $search = $this->get([$this->identity => $id])->value;
        if ($search) {
            $conn = new BaseDatos();
            if (!$this->softDeleted) {
                /* Si el modelo no tiene el softdeleted, borramos el recurso completo de la DB */
                $result = $conn->delete($this->table, [$this->identity => $id]);
            } else {
                /* Si el modelo tiene el softdeleted, modificamos la columna que afecta al softdeleted */
                $deleted_at = date("Y-m-d H:i:s", time());
                $data = $this->get([$this->identity => $id])->value;
                if (!isset($data[$this->softDeleted]) && $data[$this->softDeleted] == null) {
                    $result = $this->update([$this->softDeleted => $deleted_at], $id);
                } else {
                    $result = new ErrorException('El recurso ya se encuentra eliminado');
                }
            }

            if ($result instanceof ErrorException) {
                /* logFileEE($this->logPath, $result, get_class($this), __FUNCTION__); */
                createJsonError($this->logPath, $result, get_class($this), __FUNCTION__, ['id' => $id], $this);
            }
        } else {
            $result = new ErrorException('No se encuentra el recurso');
        }

        return $result;
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
            /* logFileEE($this->logPath, $th, get_class($this), __FUNCTION__); */
            createJsonError($this->logPath, $th, get_class($this), __FUNCTION__, null, $this);
            return $th;
        }
    }

    /** Genera el arreglo de los indices para determinar los valores unicos en la tabla  */
    public function setUniquesIndex()
    {
        $indexs = $this->executeSqlQuery('EXEC sp_helpindex ' . $this->table, false);

        if ($indexs instanceof ErrorException) {
            /* logFileEE($this->logPath, $indexs, get_class($this), __FUNCTION__); */
            createJsonError($this->logPath, $indexs, get_class($this), __FUNCTION__, null, $this);
        } else {
            $indexs = array_filter($indexs, function ($index) {
                return str_contains($index['index_description'], 'unique');
            });

            $this->uniques = array_map(
                function ($index) {
                    return $index['index_name'];
                },
                $indexs
            );
        }
    }

    /** Envia una respuesta si el motor SQL reponde un error de un valor existente */
    public function sendRepeatError($data)
    {
        if ($data instanceof ErrorException) {
            foreach ($this->uniques as $unique) {
                if (str_contains($data->getMessage(), "UNIQUE KEY '$unique'")) {
                    sendResError($data, "UNIQUE_KEY_$unique");
                }
            }
        }
    }

    private function formatPost($req)
    {
        foreach ($req as $key => $p) {
            if (!in_array($key, $this->fillable)) {
                unset($req[$key]);
            }
        }

        return $req;
    }
}
