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

    protected $fillable = ['REFERENCIA', 'AppID', 'PefilID'];
}
