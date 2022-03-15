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
class WapUsuario extends BaseModel
{
    protected $table = 'wapUsuarios';
    protected $logPath = 'v1/wapUsuarios';
    protected $identity = 'ReferenciaID';

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

    public function wapPersona()
    {
        return $this->hasOne(WapPersona::class, 'PersonaID',  'ReferenciaID');
    }
}
