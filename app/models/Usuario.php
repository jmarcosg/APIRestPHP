<?php

/**
 * This is the model class for table "wapUsuario".
 *
 * @property int $personaId
 * @property int $usuario
 * @property string $clave
 * 
 */
class Usuario
{
    public $personaId;
    public $usuario;
    public $clave;

    public function __construct()
    {
        $this->personaId = "";
        $this->usuario = "";
        $this->clave = "";
    }

    public function set($personaId = null, $usuario = null, $clave = null)
    {
        $this->personaId = $personaId;
        $this->usuario = $usuario;
        $this->clave = $clave;
    }

    public function save()
    {
        $array = json_decode(json_encode($this), true);
        $conn = new BaseDatos();
        $result = $conn->store(WAP_USUARIO, $array);

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

    public static function get($params)
    {
        $conn = new BaseDatos();
        $result = $conn->search(WAP_USUARIO, $params);
        $usuario = $conn->fetch_assoc($result);

        /* Guardamos los errores */
        if ($conn->getError()) {
            $error =  $conn->getError() . ' | Error a obtener la solicitud: ' . $params['id'];
            cargarLogFile('get_user', $error, get_class(), __FUNCTION__);
        }
        return $usuario;
    }

    public static function update($table, $res, $id, $column)
    {
        $conn = new BaseDatos();
        $result = $conn->update($table, $res, $id, $column);

        /* Guardamos los errores */
        if ($conn->getError()) {
            $error =  $conn->getError() . ' | Error a modificar un formulario ' . $id;
            cargarLogFile('get_user', $error, get_class(), __FUNCTION__);
        }
        return $result;
    }
}
