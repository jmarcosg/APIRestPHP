<?php

namespace App\Models\Weblogin;

use App\Models\BaseModel;
use ErrorException;

class WlApp extends BaseModel
{
    protected $table = 'wapAppsRecientes';
    protected $logPath = 'v1/WebLogin';
    protected $identity = 'id';
}
