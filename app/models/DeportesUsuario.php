<?php

namespace App\Models;

/**
 * This is the model class for table "DeportesUsuario".
 *
 * @property int $id_wappersona
 * @property string $nombre
 * @property string $apellido
 * 
 */
class DeportesUsuario extends BaseModel
{
    protected $table = 'deportes_usuarios';
    protected $logPath = 'v1/deportes_usuarios';
    protected $identity = 'id';

    public $id_wappersonas;
    public $nombre;
    public $apellido;

    public function __construct()
    {
        $this->id_wappersonas = "";
        $this->nombre = "";
        $this->apellido = "";
    }

    public function set(array $req)
    {
        $this->id_wappersonas = array_key_exists('id_wappersonas', $req) ? $req['id_wappersonas'] : null;
        $this->nombre = array_key_exists('nombre', $req) ? $req['nombre'] : null;
        $this->apellido = array_key_exists('apellido', $req) ? $req['apellido'] : null;
    }
}
