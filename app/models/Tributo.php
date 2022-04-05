<?php

namespace App\Models;

use ErrorException;

class Tributo extends BaseModel
{
    protected $logPath = 'v1/tributo';

    protected $table = 'totems_stats';

    protected $fillable = ['tributo', 'send_type', 'periodo', 'cant_imponible'];

    public function sendEmailMensual($res)
    {
        $url = 'https://weblogin.muninqn.gov.ar/api/TotemMail';

        $data = [
            'address' => $res['address'],
            'subject' => $res['subject'],
            'htmlBody' => $_POST['htmlBody'],
            'reciboAdjunto' => $_POST['reciboAdjunto']
        ];

        return $this->sendEmail($data, $url);
    }

    public function sendEmailSemestral($res)
    {
        $url = 'https://weblogin.muninqn.gov.ar/api2/TotemEmision';

        $data = [
            'address' => $res['address'],
            'subject' => $res['subject'],
            'htmlBody' => $res['htmlBody'],
            'imponibleID' => $res['imponibleID'],
            'TR1E200_ID' => $res['TR1E200_ID']
        ];

        return $this->sendEmail($data, $url);
    }

    private function sendEmail($data, $url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response);

        if ($response->value == null && $response->error != null) {
            $error = new ErrorException($response->error);
            logFileEE($this->logPath, $error, get_class($this), __FUNCTION__);
        }

        return $response;
    }
}
