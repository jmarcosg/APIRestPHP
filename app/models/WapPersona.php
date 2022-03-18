<?php

namespace App\Models;

/**
 * This is the model class for table "wapPersona".
 *
 * @property int $RenaperID
 * @property int $CiudadanoID
 * @property int $Documento
 * 
 */
class WapPersona extends BaseModel
{
    protected $table = 'wapPersonas';
    protected $logPath = 'v1/wapPersonas';
    protected $identity = 'ReferenciaID';

    protected $fillable = ['RenaperID', 'CiudadanoID', 'Documento'];

    function wapUsuario()
    {
        return $this->hasOne(WapUsuario::class, 'ReferenciaID',  'PersonaID');
    }
}
