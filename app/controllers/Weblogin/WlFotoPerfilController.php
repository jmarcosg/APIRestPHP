<?php

namespace App\Controllers\Weblogin;

use App\Models\Weblogin\WlFotoPerfil;
use ErrorException;

class WlFotoPerfilController
{
    use SqlTrait;

    public static function getPersonasSinVerificar()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $sql = self::getPersonsSql('estado = 0');
        $data = $wlFotoPerfil->executeSqlQuery($sql, false);

        sendResError($data, 'Hubo un error en la obtención de las personas');

        $data = $wlFotoPerfil->setBase64($data);

        sendRes($data);

        exit;
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

        exit;
    }

    public static function getFotoById()
    {
        $data = new WlFotoPerfil();

        $id = $_GET['id'];
        $registro = $data->get(['id' => $id])->value;

        /* if ($doc['documento']) {
            $codigo = $doc['codigo'];
            $url = $filesUrl . $id_solicitud . "/" . $codigo . '/' .  $doc['documento'];
            $data[$key]['url'] = getBase64String($url, $doc['documento']);
            $data[$key]['loading'] = false;
            $data[$key]['error'] = false;
        } */

        sendRes($registro);

        exit;
    }

    public static function saveFoto()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $wlFotoPerfil->saveFotos(uniqid());

        $_POST['estado'] = 0;
        $_POST['estado_app'] = 0;

        $wlFotoPerfil->set($_POST);

        $id = $wlFotoPerfil->save();

        sendResError($id, 'Hubo un error al guardar la foto');

        $data = $wlFotoPerfil->get(['id' => $id])->value;

        if ($data) {
            $data = $wlFotoPerfil->setBase64($data);
            sendRes($data);
        } else {
            sendRes(null, 'No se encontraron registros');
        }

        exit;
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
                $data = $wlFotoPerfil->update(['foto_perfil' => $_POST['foto_perfil']], $id);

                if ($data) {
                    $wlFotoPerfil->deleteFoto($perfil);
                } else {
                    sendRes(null, 'Hubo un problema al modificar la foto de perfil');
                }
            }

            if (isset($_FILES['foto_dni'])) {
                $dni = $registro['foto_dni'];

                $wlFotoPerfil->saveFotoDni($uniqid);
                $data = $wlFotoPerfil->update(['foto_dni' => $_POST['foto_dni']], $id);

                if ($data) {
                    $wlFotoPerfil->deleteFoto($dni);
                } else {
                    sendRes(null, 'Hubo un problema al modificar la foto del DNI');
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

    public static function changeEstado()
    {
        $wlFotoPerfil = new WlFotoPerfil();

        $id = $_POST['id'];

        $registro = $wlFotoPerfil->get(['id' => $id])->value;

        if ($registro) {
            $wlFotoPerfil->verifyEstados($registro);

            $data = false;

            if (isset($_POST['estado'])) {
                $data = $wlFotoPerfil->update($_POST, $id);
            }

            if (isset($_POST['estado_app'])) {
                $data = $wlFotoPerfil->update($_POST, $id);
            }

            if ($data) {
                $registro = $wlFotoPerfil->get(['id' => $id])->value;
                $data = $wlFotoPerfil->setBase64($registro);
                sendRes($data);
            } else {
                sendRes(null, 'Hubo un error al modificar el estado');
            }
        } else {
            sendRes(null, 'No se encontraron registros');
        }
        exit;
    }
}
