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
            $id = $result['id'];
            $contents = self::getContentSql("id_usuario = '$id'");

            sendResError($contents, 'Hubo un error inesperado');

            $contents = self::formatContents($contents);

            $result['is_admin'] = $result['is_admin'] == "0" ? false : true;
            $result['contents'] = $contents;
            sendRes($result);
        } else {
            sendRes(null, 'Credenciales invalidas');
        }
    }

    public static function saveContent()
    {
        $data = new IdeasPropuestas();
        $data->set($_POST);

        $id = $data->save();

        sendResError($id, 'Hubo un error al guardar su idea, intente mas tarde');

        $contents = self::getContentSql(self::getWhereSql());

        sendResError($contents, 'Hubo un error al obtener las ideas');

        $contents = self::formatContents($contents);

        sendRes($contents);
    }

    public static function saveEditContent()
    {
        $data = new IdeasPropuestas();

        $params = ['content' => $_POST['content'], 'id_categoria' => $_POST['id_categoria'], 'barrio' => $_POST['barrio']];
        $data = $data->update($params, $_POST['id']);

        if ($data) {
            sendResError($data, 'Hubo un error al guardar su idea, intente mas tarde');

            $contents = self::getContentSql(self::getWhereSql());

            sendResError($contents, 'Hubo un error al obtener las ideas');

            $contents = self::formatContents($contents);

            sendRes($contents);
        } else {
            sendRes(null, 'Hubo un error al guardar la idea');
        }
    }

    public static function deleteContent()
    {
        $data = new IdeasPropuestas();

        $data = $data->delete($_POST['id']);

        sendResError($data, 'Hubo un error al obtener las ideas');

        if ($data) {
            $contents = self::getContentSql(self::getWhereSql());

            sendResError($contents, 'Hubo un error al obtener las ideas');

            $contents = self::formatContents($contents);

            sendRes($contents, null);
        } else {
            sendRes(null, 'No se pudo eliminar el recurso');
        }
    }

    public static function getContents($where = "1=1")
    {
        $contents = self::getContentsSql($where);

        sendResError($contents, 'Hubo un error inesperado');

        sendRes($contents);
    }

    public static function getContentsByUser()
    {
        $resultInterno = self::getContentsSqlByUser();

        sendResError($resultInterno, 'Hubo un error inesperado');

        $resultExterno = self::getContentsSqlByUser('externo');

        sendResError($resultInterno, 'Hubo un error inesperado');

        $result = array_merge($resultInterno, $resultExterno);

        sendRes($result);
    }

    public static function getCountContentsByCat()
    {
        $result = self::getContentsSqlByCat();

        sendResError($result, 'Hubo un error inesperado');

        sendRes($result);
    }

    public static function getUsuarios()
    {
        $data = new Usuarios();

        $usuarios = $data->list();

        sendResError($usuarios, 'Hubo un error al obtener los usuarios');

        sendRes($usuarios->value);
    }

    public static function getCategorias()
    {
        $data = new Categorias();

        $categorias = $data->list();

        sendResError($categorias, 'Hubo un error al obtener los usuarios');

        sendRes($categorias->value);
    }

    public static function getContentsByCat()
    {
        $cat = $_GET['categoria'];
        $contents = self::getContentsSql("cat.nombre =  '$cat'");

        sendResError($contents, 'Hubo un error inesperado');

        sendRes($contents);
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
    }

    private static function formatContents($contents)
    {
        foreach ($contents as $key => $content) {
            if (!$content['id_categoria']) $contents[$key]['id_categoria'] = 'DEFAULT';
        }
        return $contents;
    }

    private static function getWhereSql()
    {
        if (key_exists('id_usuario_wl', $_POST)) {
            $id = $_POST['id_usuario_wl'];
            $where = "id_usuario_wl = '$id'";
        }

        if (key_exists('id_usuario', $_POST)) {
            $id = $_POST['id_usuario'];
            $where = "id_usuario = '$id'";
        }

        return $where;
    }
}
