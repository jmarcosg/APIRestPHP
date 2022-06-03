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

    public function getDataTramite($gender, $dni, $tramite)
    {
        try {
            $token = $this->getTokenRenaper();

            $curl = curl_init();

            $post = json_encode([
                "genero" => $gender,
                'numero' => $dni,
                "tramite" => $tramite
            ]);
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://weblogin.muninqn.gov.ar/api/RenaperExt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS
                => json_encode([
                    "genero" => $gender,
                    'numero' => $dni,
                    "tramite" => $tramite
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
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
