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
class WlAplicacion extends BaseModel
{
    protected $table = 'wlAplicaciones';
    protected $logPath = 'v1/wlAplicacion';
    protected $identity = 'REFERENCIA';

    public $REFERENCIA;
    public $AppID;
    public $PefilID;

    public function __construct()
    {
        parent::__construct();

        $this->addFilterMethod(['set']);
        
        $this->REFERENCIA = "";
        $this->AppID = "";
        $this->PefilID = "";
    }

    public function set(array $req)
    {
        $this->REFERENCIA = array_key_exists('REFERENCIA', $req) ? $req['REFERENCIA'] : null;
        $this->AppID = array_key_exists('AppID', $req) ? $req['AppID'] : null;
        $this->PefilID = array_key_exists('PefilID', $req) ? $req['PefilID'] : null;
    }
}
