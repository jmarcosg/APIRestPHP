<?php

namespace App\Models\Weblogin;

use App\Models\BaseModel;
use ErrorException;

class Weblogin extends BaseModel
{
    public static function saveLog($error, $class, $function)
    {
        if (!$error instanceof ErrorException) {
            $error = new ErrorException($error);
        }
        logFileEE('v1/weblogin', $error, $class, $function);
    }
}
