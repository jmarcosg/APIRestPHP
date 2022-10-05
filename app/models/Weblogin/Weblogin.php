<?php

namespace App\Models\Weblogin;

use App\Models\BaseModel;
use ErrorException;

class Weblogin extends BaseModel
{
    protected $logPath = 'v1/WebLogin';

    public static function saveLog($error, $class, $function)
    {
        if (!$error instanceof ErrorException) {
            $error = new ErrorException($error);
        }
        logFileEE(self::$logPath, $error, $class, $function);
    }
}
