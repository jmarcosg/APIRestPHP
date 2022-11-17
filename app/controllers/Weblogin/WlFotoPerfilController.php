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

        if (count($data) == 0) {
            sendRes($data, 'N hay informacion');
        }
        sendResError($data, 'Hubo un error en la obtención de las personas');

        $data = $wlFotoPerfil->setBase64($data);

        sendRes($data);
    }

    public static function getLastFotos()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $data = $wlFotoPerfil->get($_POST, ['order' => ' ORDER BY id DESC'])->value;

        if ($data) {
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
        } else {
            sendRes(null, 'No se encontraron registros');
        }
    }

    public static function getFotoById()
    {
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
        $wlFotoPerfil = new WlFotoPerfil();

        $id = $_POST['id'];

        $registro = $wlFotoPerfil->get(['id' => $id])->value;

        /* Verifica si encuenta el registro sin evaluar */
        $wlFotoPerfil->verifyEstados($registro);

        if (isset($_POST['estado'])) {

            /* Si es aprobado guardamos la foto */
            if ($_POST['estado'] == '1') $wlFotoPerfil->setFotoRenaper();

            if (isset($_POST['img'])) unset($_POST['img']);
            if (isset($_POST['dni'])) unset($_POST['dni']);

            /* Actualizamos los registros en la base de datos */
            $data = $wlFotoPerfil->update($_POST, $id);

            if ($data) {
                $registro = $wlFotoPerfil->get(['id' => $id])->value;
                $data = $wlFotoPerfil->setBase64($registro);
                sendRes($data);
            } else {
                sendRes(null, 'Hubo un problema para actulizar el registro');
            }
        } else {
            sendRes(null,  'Requiere estado');
        }
    }
}
