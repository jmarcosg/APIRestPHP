<?php

namespace App\Models;

/**
 * This is the model class for table "wlAplicaciones".
 *
 * @property int $ReferenciaID
 * @property int $AppID
 * @property int $PefilID
 * 
 */
class WapUsuariosPerfiles extends BaseModel
{
    protected $table = 'wapUsuariosPerfiles';
    protected $logPath = 'v1/wapUsuariosPerfiles';
    protected $identity = 'ReferenciaID';

    public $ReferenciaID;
    public $AppID;
    public $PefilID;

    public function __construct()
    {
        $this->REFERENCIA = "";
        $this->AppID = "";
        $this->PefilID = "";
    }

    public function set(array $req)
    {
        $this->ReferenciaID = array_key_exists('ReferenciaID', $req) ? $req['ReferenciaID'] : null;
        $this->AppID = array_key_exists('AppID', $req) ? $req['AppID'] : null;
        $this->PefilID = array_key_exists('PefilID', $req) ? $req['PefilID'] : null;
    }

    function wapUsuario()
    {
        return $this->hasOne(WapUsuario::class, 'ReferenciaID',  'ReferenciaID');
    }

    function wlAplicacion()
    {
        return $this->hasOne(WlAplicacion::class, 'AppID',  'REFERENCIA');
    }
}
