<?php

namespace App\Connections;

class BaseDatos
{
    private $conn_string;
    private $user;
    private $host;
    private $pass;
    public $db;
    private $conn;
    private $charset;

    public function __construct()
    {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->db = DB_NAME;
        $this->charset = DB_CHARSET;
    }

    public function connect()
    {
        $this->conn_string = 'DRIVER={SQL Server};SERVER=' . $this->host . ';DATABASE=' . $this->db . ';charset=' . $this->charset;
        $this->conn = odbc_connect($this->conn_string, $this->user, $this->pass);
    }

    public function search($table, $param = [], $ops = [])
    {
        try {
            $this->connect();
            $where = " 1=1 ";
            $values = array();
            foreach ($param as $key => $value) {
                if ($key == 'TOP') continue;
                $op = "=";
                if (isset($value)) {
                    /* if (isset($ops[$key])) {
                        $op = $ops[$key];
                    } */
                    $where .= " AND " . $key . $op . "'$value'";
                    $values[] = $value;
                } else {
                    $where .= " AND " . $key . " is null";
                }
            }

            $limit = array_key_exists('TOP', $param) ? 'TOP ' . $param['TOP'] : '';
            $order = array_key_exists('order', $ops) ? $ops['order'] : '';

            $sql = "SELECT $limit * FROM " . $table . " WHERE " . $where . $order;
            $query = odbc_exec($this->conn, $sql);
            return $query;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function store($table, $params)
    {
        try {
            $this->connect();
            $count = count($params);
            $strKeys = "(" . implode(" ,", array_keys($params)) . ")";
            $strVals = "(?" . str_repeat(",?", $count - 1) . ")";
            $sql = "INSERT INTO $table$strKeys VALUES " . $strVals;

            $query = $this->prepare($sql);
            return $this->executeQuery($query, $params, true);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function update($table, $params, $id, $column = 'id')
    {
        try {
            $this->connect();
            $strKeyValues = '';
            $values = [];
            foreach ($params as $key => $value) {
                $strKeyValues .= "$key = ?,";
                $values[] = $value;
            }
            $values[] = $id;

            $strKeyValues = trim($strKeyValues, ',');

            $sql = "UPDATE $table SET $strKeyValues WHERE $column=?";
            $query = $this->prepare($sql);
            return $this->executeQuery($query, $values);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function delete($table, $param = [], $ops = [])
    {
        try {
            $this->connect();
            $where = " 1=1 ";
            $values = array();
            foreach ($param as $key => $value) {
                $op = "=";
                if (isset($value)) {
                    if (isset($ops[$key])) {
                        $op = $ops[$key];
                    }
                    $where .= " AND " . $key . $op . $value;
                    $values[] = $value;
                }
            }

            $sql = "DELETE FROM " . $table . " WHERE " . $where;
            $query = $this->prepare($sql);
            return $this->executeQuery($query, $param);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function prepare($sql)
    {
        return odbc_prepare($this->conn, $sql);
    }

    public function fetch_assoc($result)
    {
        return odbc_fetch_array($result);
    }

    /**
     * Ejecuta una sentencia preparada con los parametros provistos.
     * 
     * Dado un resource de una sentencia SQL preparada, unifica los valores parametrizados.
     * Si se busca ejecutar una instruccion SQL no preparada, se debe utilizar Query($sql).
     *  @param  resource $stmt query preparada anteriormente con prepareQuery
     *  @param  boolean $alta true si la query es una alta para retornar el ID, false en otro caso
     *  @param  array $parameters array de parametros con los cuales instanciar la query preparada
     *  @return int|bool ID si $alta es true, true|false en otro caso
     */
    function executeQuery($stmt, $parameters, $alta = false)
    {
        $parameters = deutf8ize($parameters);
        odbc_exec($this->conn, "SET NOCOUNT ON");
        $ret = odbc_execute($stmt, $parameters);
        if ($alta) {
            $r = odbc_exec($this->conn, "SELECT @@IDENTITY AS ID");
            odbc_fetch_into($r, $row);
            $ret = $row[0];
        }
        return $ret;
    }

    public function query($query)
    {
        $this->connect();
        return odbc_exec($this->conn, $query);
    }

    function numRows($query_result)
    {
        return odbc_num_rows($query_result);
    }

    function getError()
    {
        return odbc_error($this->conn);
    }
}
