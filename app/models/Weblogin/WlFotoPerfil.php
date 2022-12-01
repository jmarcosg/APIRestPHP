<?php

namespace App\Models\Weblogin;

use App\Models\BaseModel;
use App\Traits\WebLogin\ValidacionesWlFotos;

class WlFotoPerfil extends BaseModel
{
    use ValidacionesWlFotos;

    protected $table = 'wlFotosUsuarios';
    protected $identity = 'id';
    protected $logPath = 'v1/wlFotosUsuarios';

    protected $fillable = [
        'id_usuario',
        'id_persona',
        'foto_perfil',
        'foto_dni',
        'id_app',
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
        $nameFile = $_POST['nombre_archivo'];

        $foto_perfil = $_FILES['foto_perfil'];
        $nameFilePerfil = $nameFile . '_PERFIL_' . $uniqid . getExtFile($foto_perfil);
        $path_perfil = getPathFile($foto_perfil, $this->filesUrl, $nameFilePerfil);

        if (comprimirImagen($foto_perfil, $foto_perfil['type'], $path_perfil)) {
            $_POST['foto_perfil'] = $nameFilePerfil;
        } else {
            sendRes(null, 'No se guardo la foto de perfil');
        }
    }

    public function saveFotoDni($uniqid)
    {
        $nameFile = $_POST['nombre_archivo'];

        $foto_dni = $_FILES['foto_dni'];
        $nameFileDni = $nameFile . '_DNI_' . $uniqid . getExtFile($foto_dni);
        $path_dni = getPathFile($foto_dni, $this->filesUrl, $nameFileDni);

        if (comprimirImagen($foto_dni, $foto_dni['type'], $path_dni)) {
            $_POST['foto_dni'] = $nameFileDni;
        } else {
            sendRes(null, 'No se guardo la foto del DNI');
        }
    }

    /** Si existe un registro sin ser evaluada */
    public function verifyEstados($data)
    {
        if (!$data) {
            sendRes(null, 'No se encontraron registros');
        }

        if ($data['estado'] != "0") {
            sendRes(null, 'Ya fue evaluada');
        }
    }

    public function deleteFoto($foto)
    {
        $url = $this->filesUrl . $foto;
        unlink($url);
    }

    public function setBase64($data)
    {

        if ($data['estado'] != 1) {
            $url = $this->filesUrl . $data['foto_perfil'];
        } else {
            $genero = substr($data['foto_perfil'], 0, 1);
            if ($genero == 'M') $url = PATH_RENAPER . 'MASCULINO\\' . $data['foto_perfil'];
            if ($genero == 'F') $url = PATH_RENAPER . 'FEMENINO\\' . $data['foto_perfil'];
            if ($genero == 'X') $url = PATH_RENAPER . 'NO BINARIO\\' . $data['foto_perfil'];
        }

        if (file_exists($url)) {
            $data['foto_perfil'] = getBase64String($url, $data['foto_perfil']);
        } else {
            $data['foto_perfil'] = 'FIN FOTO';
        }

        $url = $this->filesUrl . $data['foto_dni'];
        if (file_exists($url)) {
            $data['foto_dni'] = getBase64String($url, $data['foto_dni']);
        } else {
            $data['foto_dni'] = 'FIN FOTO';
        }

        return $data;
    }

    public function setFotoRenaper($genero, $dni)
    {
        if ($genero == 'M') $newPath = PATH_RENAPER . 'MASCULINO\\';
        if ($genero == 'F') $newPath = PATH_RENAPER . 'FEMENINO\\';
        if ($genero == 'X') $newPath = PATH_RENAPER . 'NO BINARIO\\';

        $nameFile = $genero . $dni . '.png';
        $path = getPathFile($_FILES['img'], $newPath, $nameFile);

        if (file_exists($path)) {
            sendRes(null, 'Ya existe un archivo en la carpeta de renaper');
        }

        if (!copy($_FILES['img']['tmp_name'], $path)) {
            sendRes(null, 'No se copia correctamente el archivo');
        }

        $_POST['foto_perfil'] = $nameFile;
    }
}
