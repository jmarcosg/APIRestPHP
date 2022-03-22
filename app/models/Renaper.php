<?php

namespace App\Models;

use App\Connections\BaseDatos;
use ErrorException;
use Exception;

class Renaper extends BaseModel
{
    protected $logPath = 'v1/renaper';

    public function getData($gender, $dni)
    {
        try {
            $token = $this->getTokenRenaper();

            $op = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $url = "https://weblogin.muninqn.gov.ar/api/Renaper/$token/" . $gender . $dni;
            $response = file_get_contents($url, false, stream_context_create($op));
            $result = json_decode($response);

            if ($result->error) {
                throw new ErrorException($result->error);
            } else {
                return $result->docInfo;
            }
        } catch (\Throwable $th) {
            logFileEE($this->logPath, $th, get_class($this), __FUNCTION__);
            return $th;
        }
    }

    public function getTokenRenaper()
    {
        try {
            $conn = new BaseDatos();
            $token = $conn->search('RenaperAuthToken');
            return $conn->fetch_assoc($token)['Token'];
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
