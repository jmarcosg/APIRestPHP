<?php

namespace App\Models\Weblogin;

use App\Models\BaseModel;

class WlFotoPerfil extends BaseModel
{
    protected $table = 'wlFotosUsuarios';
    protected $identity = 'id';
    protected $logPath = 'v1/wlFotosUsuarios';

    protected $fillable = [
        'id_usuario',
        'id_persona',
        'foto_perfil',
        'foto_dni',
        'id_app',
        'id_usuario_admin_app',
        'estado_app',
        'id_usuario_admin',
        'estado',
        'observacion',
    ];

    public $filesUrl = FILE_PATH . 'wlFotosUsuarios/';

    /** Guarda los arhivos fisicamente */
    public function saveFotos($uniqid)
    {
        $this->saveFotoPerfil($uniqid);
        $this->saveFotoDni($uniqid);
        unset($_POST['nombre_archivo']);
    }

    public function saveFotoPerfil($uniqid)
    {
        if (isset($_FILES['foto_perfil']) && isset($_POST['nombre_archivo'])) {
            $nameFile = $_POST['nombre_archivo'];

            $foto_perfil = $_FILES['foto_perfil'];
            $nameFilePerfil = $nameFile . '_PERFIL_' . $uniqid . getExtFile($foto_perfil);
            $path_perfil = getPathFile($foto_perfil, $this->filesUrl, $nameFilePerfil);

            if (copy($foto_perfil['tmp_name'], $path_perfil)) {
                $_POST['foto_perfil'] = $nameFilePerfil;
            } else {
                sendRes(null, 'No se guardo la foto de perfil');
                exit;
            }
        } else {
            sendRes(null, 'Los parametros son incorrectos', $_POST);
            exit;
        }
    }

    public function saveFotoDni($uniqid)
    {
        if (isset($_FILES['foto_dni']) && isset($_POST['nombre_archivo'])) {
            $nameFile = $_POST['nombre_archivo'];

            $foto_dni = $_FILES['foto_dni'];
            $nameFileDni = $nameFile . '_DNI_' . $uniqid . getExtFile($foto_dni);
            $path_dni = getPathFile($foto_dni, $this->filesUrl, $nameFileDni);

            if (copy($foto_dni['tmp_name'], $path_dni)) {
                $_POST['foto_dni'] = $nameFileDni;
            } else {
                sendRes(null, 'No se guardo la foto del DNI');
                exit;
            }
        } else {
            sendRes(null, 'Los parametros son incorrectos', $_POST);
            exit;
        }
    }

    public function verifyEstados($data)
    {
        /* Si fuera evaluada por alguna entidad */
        if ($data['estado'] === 1) {
            sendRes(null, 'Ya fue evaluada por soporte modernización', $data);
            exit;
        }

        if ($data['estado_app'] === 1) {
            sendRes(null, 'Ya fue evaluada por la aplicación', $data);
            exit;
        }
    }

    public function deleteFoto($foto)
    {
        $url = $this->filesUrl . $foto;
        unlink($url);
    }

    public function setBase64($data)
    {
        if (is_multi_array($data)) {
            foreach ($data as $key => $reg) {
                $url = $this->filesUrl . $reg['foto_perfil'];
                $data[$key]['foto_perfil'] = getBase64String($url, $reg['foto_perfil']);

                $url = $this->filesUrl . $reg['foto_dni'];
                $data[$key]['foto_dni'] = getBase64String($url, $reg['foto_dni']);
            }
        } else {
            $url = $this->filesUrl . $data['foto_perfil'];
            $data['foto_perfil'] = getBase64String($url, $data['foto_perfil']);

            $url = $this->filesUrl . $data['foto_dni'];
            $data['foto_dni'] = getBase64String($url, $data['foto_dni']);
        }
        return $data;
    }
}
