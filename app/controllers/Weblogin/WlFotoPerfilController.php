<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\WlFotoPerfil;

class WlFotoPerfilController
{
    use SqlTrait;

    public static function getPersonasSinVerificar()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $sql = self::getPersonsSql('estado = 0');
        $data = $wlFotoPerfil->executeSqlQuery($sql, false);

        sendResError($data, 'Hubo un error en la obtención de las personas');

        if (count($data) == 0) {
            sendRes(null, 'No hay informacion');
        }

        sendRes($data);
    }

    public static function getLastFotos()
    {
        WlFotoPerfil::checkParams(__FUNCTION__);

        $wlFotoPerfil = new WlFotoPerfil();

        $dni = $_POST['dni'];

        $where = "(wapPer.Documento = '$dni' OR wapPerUsr.Documento = '$dni')";
        $sql = self::getPersonsSql($where);
        $data = $wlFotoPerfil->executeSqlQuery($sql);

        sendResError($data);

        if ($data) {
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
        } else {
            sendRes(null, 'No se encontraron registros');
        }
    }

    public static function getFotoById()
    {
        WlFotoPerfil::checkParams(__FUNCTION__);

        $wlFotoPerfil = new WlFotoPerfil();

        $id = $_GET['id'];

        $sql = self::getPersonsSql("id = $id");
        $data = $wlFotoPerfil->executeSqlQuery($sql);

        sendResError($data, 'Hubo un error en la obtención de la foto', ['id' => $id]);

        if ($data) {
            sendResError($data, 'Hubo un error en la obtención de la foto', ['id' => $id]);
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
        } else {
            sendRes($data, 'No se encuentra la foto', ['id' => $id]);
        }
    }

    public static function saveFoto()
    {
        WlFotoPerfil::checkParams(__FUNCTION__);

        $wlFotoPerfil = new WlFotoPerfil();

        $wlFotoPerfil->saveFotos(uniqid());

        $_POST['estado'] = 0;

        $wlFotoPerfil->set($_POST);

        $id = $wlFotoPerfil->save();

        sendResError($id, 'Hubo un error al guardar la foto', $_POST);

        $data = $wlFotoPerfil->get(['id' => $id])->value;

        if ($data) {
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
        } else {
            sendRes(null, 'No se encontraron registros');
        }
    }

    public static function editFotoByUser()
    {
        WlFotoPerfil::checkParams(__FUNCTION__);

        $wlFotoPerfil = new WlFotoPerfil();


        $id = $_POST['id'];

        $registro = $wlFotoPerfil->get(['id' => $id])->value;

        if ($registro) {
            $wlFotoPerfil->verifyEstados($registro);

            $uniqid = uniqid();
            $data = false;

            if (isset($_FILES['foto_perfil'])) {
                $perfil = $registro['foto_perfil'];
                $wlFotoPerfil->saveFotoPerfil($uniqid);
                $data = self::updateFoto($perfil, $_POST, $id);

                if (!$data) {
                    sendResError($data, 'Hubo un error al actualizar la foto de perfil', $_POST);
                }
            }

            if (isset($_FILES['foto_dni'])) {

                $dni = $registro['foto_dni'];
                $wlFotoPerfil->saveFotoDni($uniqid);
                $data = self::updateFoto($dni, $_POST, $id);

                if (!$data) {
                    sendResError($data, 'Hubo un error al actualizar la foto del dni', $_POST);
                }
            }

            if ($data) {
                $registro = $wlFotoPerfil->get(['id' => $id])->value;
                $data = $wlFotoPerfil->setBase64($registro);
                sendRes($data);
            } else {
                sendRes(null, 'El registro se modifico, pero no se pudieron obtener los datos');
            }
        } else {
            sendRes(null, 'No se encontraron registros');
        }

        exit;
    }

    private static function updateFoto($foto, $params, $id)
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $data = $wlFotoPerfil->update($params, $id);

        if ($data) {
            $wlFotoPerfil->deleteFoto($foto);
            return true;
        } else {
            return false;
        }
    }

    public static function changeEstado()
    {
        WlFotoPerfil::checkParams(__FUNCTION__);

        $wlFotoPerfil = new WlFotoPerfil();

        $id = $_POST['id'];

        $sql = self::getPersonsSql("id = $id");
        $registro = $wlFotoPerfil->executeSqlQuery($sql);

        /* Verifica si encuenta el registro sin evaluar */
        $wlFotoPerfil->verifyEstados($registro);

        /* Si es aprobado guardamos la foto */
        if ($_POST['estado'] == '1') {
            $wlFotoPerfil->setFotoRenaper($registro['genero'], $registro['dni']);
        }

        if (isset($_POST['img'])) unset($_POST['img']);

        /* Actualizamos los registros en la base de datos */
        $data = $wlFotoPerfil->update($_POST, $id);

        if ($data) {
            $sql = self::getPersonsSql("id = $id");
            $registro = $wlFotoPerfil->executeSqlQuery($sql);
            $data = $wlFotoPerfil->setBase64($registro);
            sendRes($data);
        } else {
            sendRes(null, 'Hubo un problema para actulizar el registro');
        }
    }
}
