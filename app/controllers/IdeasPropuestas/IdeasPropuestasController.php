<?php

namespace App\Controllers\IdeasPropuestas;

use App\Models\BaseModel;

class IdeasPropuestasController
{
    public static function login()
    {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        $sql =
            "SELECT 
                nombre,
                legajo,
                dni,
                usuario,
                password ,
                tipo,
                categoria,
                secretaria,
                subsecretaria,
                info    
            FROM dbo.ip_usuarios
            WHERE usuario = '$usuario' AND password = '$password'";

        $model = new BaseModel();
        $result = $model->executeSqlQuery($sql);

        sendResError($result, 'Hubo un error inesperado');

        if ($result) {
            sendRes($result);
        } else {
            sendRes(null, 'Credenciales invalidas');
        }
        exit;
    }
}
