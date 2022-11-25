<?php

namespace App\Controllers\IdeasPropuestas;

use App\Models\IdeasPropuestas\Categorias;
use App\Models\IdeasPropuestas\IdeasPropuestas;
use App\Models\IdeasPropuestas\Usuarios;

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

            $contents = self::formatContents($contents);

            $result['is_admin'] = $result['is_admin'] == "0" ? false : true;
            $result['contents'] = $contents;
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

        $contents = self::getContentSql($_POST['id_usuario']);

        sendResError($contents, 'Hubo un error al obtener las ideas');

        $contents = self::formatContents($contents);

        sendRes($contents);
        exit;
    }

    public static function saveEditContent()
    {
        $data = new IdeasPropuestas();

        $params = ['content' => $_POST['content'], 'id_categoria' => $_POST['id_categoria']];
        $data = $data->update($params, $_POST['id']);

        if ($data) {
            sendResError($data, 'Hubo un error al guardar su idea, intente mas tarde');

            $contents = self::getContentSql($_POST['id_usuario']);

            sendResError($contents, 'Hubo un error al obtener las ideas');

            $contents = self::formatContents($contents);

            sendRes($contents);
        } else {
            sendRes(null, 'Hubo un error al guardar la idea');
        }
        exit;
    }

    public static function deleteContent()
    {
        $data = new IdeasPropuestas();

        $data = $data->delete($_POST['id']);

        if ($data) {
            $contents = self::getContentSql($_POST['id_usuario']);

            sendResError($contents, 'Hubo un error al obtener las ideas');

            $contents = self::formatContents($contents);

            sendRes($contents, null);
        } else {
            sendRes(null, 'No se pudo eliminar el recurso');
        }

        exit;
    }

    public static function getContents($where = "1=1")
    {
        $contents = self::getContentsSql($where);

        sendResError($contents, 'Hubo un error inesperado');

        $contents = self::formatContents($contents);

        sendRes($contents);

        exit;
    }

    public static function getContentsByUser()
    {
        $result = self::getContentsSqlByUser();

        sendResError($result, 'Hubo un error inesperado');

        sendRes($result);

        exit;
    }

    public static function getUsuarios()
    {
        $data = new Usuarios();

        $usuarios = $data->list();

        sendResError($usuarios, 'Hubo un error al obtener los usuarios');

        sendRes($usuarios->value);

        exit;
    }

    public static function getCategorias()
    {
        $data = new Categorias();

        $categorias = $data->list();

        sendResError($categorias, 'Hubo un error al obtener los usuarios');

        sendRes($categorias->value);

        exit;
    }

    public static function saveUser()
    {
        $data = new Usuarios();
        $data->set($_POST);

        $id = $data->save();

        $data->sendRepeatError($id);

        sendResError($id, 'Hubo un error al guardar el usuario');

        $usuarios = $data->list();

        sendResError($usuarios, 'Hubo un error al obtener los usuarios');

        sendRes($usuarios->value);
        exit;
    }

    private static function formatContents($contents)
    {
        foreach ($contents as $key => $content) {
            if (!$content['id_categoria']) $contents[$key]['id_categoria'] = 'DEFAULT';
        }
        return $contents;
    }
}
