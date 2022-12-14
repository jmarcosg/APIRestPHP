<?php

namespace App\Models\Weblogin;

use App\Models\Weblogin\Weblogin;

class WapAppsRecientes extends Weblogin
{
    protected $table = 'wapAppsRecientes';
    protected $identity = 'id';

    protected $fillable = [
        'id_usuario', 'id_app', 'ingresos', 'fecha_ingreso', 'fecha_referencia'
    ];
}
