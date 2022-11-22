<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\WlFotoPerfil;
use App\Traits\WebLogin\Validaciones;

class WlFotoPerfilController
{
    use SqlTrait, Validaciones;

    public static function getPersonasSinVerificar()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $sql = self::getPersonsSql('estado = 0');
        $data = $wlFotoPerfil->executeSqlQuery($sql, false);

        if (count($data) == 0) {
            sendRes($data, 'No hay informacion');
        }
        sendResError($data, 'Hubo un error en la obtención de las personas');

        $data = $wlFotoPerfil->setBase64($data);

        sendRes($data);
    }

    public static function getLastFotos()
    {
        self::checkParams(__FUNCTION__);

        $wlFotoPerfil = new WlFotoPerfil();

        $dni = $_POST['dni'];

        $where = "(wapPer.Documento = '$dni' OR wapPerUsr.Documento = '$dni') AND fUsr.estado = 0";
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
        self::checkParams(__FUNCTION__);

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
        self::checkParams(__FUNCTION__);

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
        self::checkParams(__FUNCTION__);

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
                $params = ['foto_perfil' => $_POST['foto_perfil']];
                $data = self::updateFoto($perfil, $uniqid, $params, $id);

                if (!$data) {
                    sendResError($data, 'Hubo un error al actualizar la foto de perfil', $params);
                }
            }

            if (isset($_FILES['foto_dni'])) {

                $dni = $registro['foto_dni'];
                $wlFotoPerfil->saveFotoDni($uniqid);
                $params = ['foto_dni' => $_POST['foto_dni']];
                $data = self::updateFoto($dni, $uniqid, $params, $id);

                if (!$data) {
                    sendResError($data, 'Hubo un error al actualizar la foto del dni', $params);
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

    private static function updateFoto($foto, $uniqid, $params, $id)
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $wlFotoPerfil->saveFotoPerfil($uniqid);
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
        self::checkParams(__FUNCTION__);

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
