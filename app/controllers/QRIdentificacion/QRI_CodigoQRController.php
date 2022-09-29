<?php

namespace App\Controllers\QRIdentificacion;

use App\Connections\BaseDatos;
use App\Models\QRIdentificacion\QRI_Codigo_QR;
use App\Models\QRIdentificacion\QRI_Persona;

class QRI_CodigoQRController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'QRI_codigo_qr';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new QRI_Codigo_QR();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new QRI_Codigo_QR();
        $data->set($res);
        return ($data->save());
    }
}
