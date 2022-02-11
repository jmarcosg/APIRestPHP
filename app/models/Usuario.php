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
    protected $table = 'wapUsuarios';
    protected $logPath = 'v1/usuario';

    public $PersonaID;
    public $Usuario;
    public $Clave;

    public function __construct()
    {
        $this->PersonaID = "";
        $this->Usuario = "";
        $this->Clave = "";
    }

    public function set(array $req)
    {
        $this->PersonaID = array_key_exists('PersonaID', $req) ? $req['PersonaID'] : null;
        $this->Usuario = array_key_exists('Usuario', $req) ? $req['Usuario'] : null;
        $this->Clave = array_key_exists('Clave', $req) ? $req['Clave'] : null;
    }
}
