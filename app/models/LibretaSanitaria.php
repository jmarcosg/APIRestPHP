<?php

namespace App\Models;

use ErrorException;

class LibretaSanitaria extends BaseModel
{
    protected $logPath = 'v1/libreta-sanitaria';

    public function getSolicitudesWhereId($id)
    {
        $sql =
            "SELECT TOP 1
                sol.id as id,
                sol.estado as estado,
                sol.fecha_vencimiento as venc
            FROM wapUsuarios wu
                LEFT JOIN wapPersonas per ON per.ReferenciaID = wu.PersonaID
                LEFT JOIN libretas_usuarios usu ON usu.id_wappersonas = per.ReferenciaID
                LEFT JOIN libretas_solicitudes sol ON sol.id_usuario_solicitante = usu.id
            WHERE wu.ReferenciaID = $id ORDER BY id DESC";

        $result = $this->executeSqlQuery($sql);

        if ($result instanceof ErrorException) {
            logFileEE($this->logPath, $result, get_class($this), __FUNCTION__);
        } else {
            if ($result) {
                $result = $this->changeResultFormat($result);
            }
        }

        return $result;
    }

    private function changeResultFormat(array $result)
    {
        switch ($result['estado']) {
            case 'Nuevo':
                $result['estado'] = 'En revisión';
                break;
            case 'Aprobado':
                $result['estado'] =  'Aprobado';
                break;
            case 'Rechazado':
                $result['estado'] =  'Rechazado';
                break;
        }

        $result['url'] = null;
        if ($result['venc']) {
            $arrayFechas = compararFechas($result['venc'], 'days');
            if ($arrayFechas['dif'] <= 7) $result['estado'] = 'Por vencer';
            if ($arrayFechas['date'] <= $arrayFechas['now']) $result['estado'] =  'Vencida';
            $result['url'] = "https://weblogin.muninqn.gov.ar/apps/libretasanitaria/public/views/carnet/index.php?solicitud=" . $result['id'];
        }

        return $result;
    }
}
