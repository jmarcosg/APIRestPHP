<?php

namespace App\Models;

class Tributo extends BaseModel
{
    protected $logPath = 'v1/tributo';

    protected $table = 'totems_stats';

    protected $fillable = ['tributo', 'send_type', 'periodo', 'cant_imponible'];
}
