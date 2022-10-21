<?php

namespace App\Controllers\IdeasPropuestas;

use App\Models\IdeasPropuestas\IdeasPropuestas;

class IdeasPropuestasController
{
    use SqlTrait;

    public static function login()
    {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        $result = self::getUserSql($usuario, $password);

        sendResError($result, 'Hubo un error inesperado');

        if ($result) {
            $contents = self::getContentSql($result['id']);

            sendResError($contents, 'Hubo un error inesperado');

            $result['is_admin'] = $result['is_admin'] == "0" ? false : true;
            $result['contents'] = self::formatContent($contents);
            sendRes($result);
        } else {
            sendRes(null, 'Credenciales invalidas');
        }
        exit;
    }

    public static function saveContent()
    {
        $data = new IdeasPropuestas();
        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Hubo un error al guardar su idea, intente mas tarde');

        sendRes($id);
        exit;
    }

    public static function formatContent($contents)
    {
        $array = [];
        foreach ($contents as $content) {
            $array[] = $content['content'];
        }
        return $array;
    }

    public static function getContents($where = "1=1")
    {
        $result = self::getContentsSql($where);

        sendResError($result, 'Hubo un error inesperado');

        sendRes($result);

        exit;
    }

    public static function getContentsByUser()
    {
        $result = self::getContentsSqlByUser();

        sendResError($result, 'Hubo un error inesperado');

        sendRes($result);

        exit;
    }
}
