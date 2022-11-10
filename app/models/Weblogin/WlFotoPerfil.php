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

    public function saveFotos()
    {
        if (isset($_FILES['foto_perfil']) && isset($_FILES['foto_dni'])) {

            $foto_perfil = $_FILES['foto_perfil'];
            $nameFilePerfil = 'M31960202_PERFIL_' . uniqid() . getExtFile($foto_perfil);
            $path_perfil = getPathFile($foto_perfil, $this->filesUrl, $nameFilePerfil);

            if (copy($foto_perfil['tmp_name'], $path_perfil)) {
                $_POST['foto_perfil'] = $nameFilePerfil;
            } else {
                sendRes(null, 'No se guardo la foto de perfil');
            }

            $foto_dni = $_FILES['foto_dni'];
            $nameFileDni = 'M31960202_DNI_' . uniqid() . getExtFile($foto_dni);
            $path_dni = getPathFile($foto_dni, $this->filesUrl, $nameFileDni);

            if (copy($foto_dni['tmp_name'], $path_dni)) {
                $_POST['foto_dni'] = $nameFileDni;
            } else {
                sendRes(null, 'No se guardo la foto del DNI');
            }

            $_POST['estado'] = 0;
        } else {
            sendRes(null, 'Los parametros son incorrectos', $_POST);
        }
    }
}
