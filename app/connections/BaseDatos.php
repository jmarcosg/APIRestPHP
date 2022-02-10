<?php
class BaseDatos
{

    private $conn_string;
    private $user;
    private $pass;
    public $db;
    public $msj_error;
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

    /**
     * Permite realizar la busqueda de un objeto por multiples campos y si se especifica, con operadores
     * específicos.
     * @param array $param arreglo del direccion 'campo' => 'valor buscado' o vacio si se necesitan listar todos
     * @param array $ops arreglo opcional del direccion 'campo' => 'operador', por defecto el operador es '='
     * @return Usuario[]
     */
    public function search($table, $param = [], $ops = [])
    {
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

        $sql = "SELECT * FROM " . $table . " WHERE " . $where;
        $query = odbc_exec($this->conn, $sql);
        if ($query) {
            return $query;
        } else {
            return false;
        };
    }
    public function searchOrderBy($table, $param = [], $ops = [], $orderBy, $order)
    {
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

        $sql = "SELECT * FROM " . $table . " WHERE " . $where . " ORDER BY " . $orderBy . " " . $order;
        $query = odbc_exec($this->conn, $sql);
        if ($query) {
            return $query;
        } else {
            return false;
        };
    }
    public function store($table, $params)
    {
        $this->connect();
        $count = count($params);
        $strKeys = "(" . implode(" ,", array_keys($params)) . ")";
        $strVals = "(?" . str_repeat(",?", $count - 1) . ")";
        $sql = "INSERT INTO $table$strKeys VALUES " . $strVals;

        /* Ejecutamos la consulta */
        $query = $this->prepare($sql);
        return $this->executeQuery($query, $params, true);
    }

    public function update($table, $params, $id, $column = 'id')
    {
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
        $temp = odbc_exec($this->conn, "SET NOCOUNT ON");
        $ret = odbc_execute($stmt, $parameters);
        if ($alta) {
            $r = odbc_exec($this->conn, "SELECT @@IDENTITY AS ID");
            $rc = odbc_fetch_into($r, $row);
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
