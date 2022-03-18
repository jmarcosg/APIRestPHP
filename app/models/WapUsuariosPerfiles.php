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

    protected $reExectMethods = ['wapPersona'];

    protected $fillable = ['ReferenciaID', 'AppID', 'PefilID'];

    function wapUsuario()
    {
        return $this->hasOne(WapUsuario::class, 'ReferenciaID',  'ReferenciaID');
    }

    function wlAplicacion()
    {
        return $this->hasOne(WlAplicacion::class, 'AppID',  'REFERENCIA');
    }
}
