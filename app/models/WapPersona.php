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

    public $RenaperID;
    public $CiudadanoID;
    public $Documento;

    public function __construct()
    {
        parent::__construct();

        $this->addFilterMethod(['set']);
        
        $this->RenaperID = "";
        $this->CiudadanoID = "";
        $this->Documento = "";
    }

    public function set(array $req)
    {
        $this->RenaperID = array_key_exists('RenaperID', $req) ? $req['RenaperID'] : null;
        $this->CiudadanoID = array_key_exists('CiudadanoID', $req) ? $req['CiudadanoID'] : null;
        $this->Documento = array_key_exists('Documento', $req) ? $req['Documento'] : null;
    }

    function wapUsuario()
    {
        return $this->hasOne(WapUsuario::class, 'ReferenciaID',  'PersonaID');
    }
}
