<?php

namespace App\Models\Weblogin;

use App\Models\BaseModel;

class Weblogin extends BaseModel
{
    public static function saveLog($error, $class, $function)
    {
        logFileEE('v1/weblogin', $error, $class, $function);
    }
}
