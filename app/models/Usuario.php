<?php

namespace App\Models;
/**
 * This is the model class for table "wapUsuario".
 *
 * @property int $personaId
 * @property int $usuario
 * @property string $clave
 * 
 */
class Usuario extends BaseModel
{
    public $personaId;
    public $usuario;
    public $clave;
    protected $table = 'wapUsuarios';
    protected $logPath = 'v1/usuario';

    public function __construct()
    {
        $this->personaId = "";
        $this->usuario = "";
        $this->clave = "";
    }

    public function set($personaId = null, $usuario = null, $clave = null)
    {
        $this->personaId = $personaId;
        $this->usuario = $usuario;
        $this->clave = $clave;
    }
}
