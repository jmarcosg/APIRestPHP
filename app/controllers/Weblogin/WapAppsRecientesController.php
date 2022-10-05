<?php


namespace App\Controllers\Weblogin;

use App\Models\WebLogin\WapAppsRecientes;
use DateInterval;
use DateTime;
use ErrorException;

class WapAppsRecientesController
{
    /** Cuando se ingresa a una APP, se debe sumar 1 ingreso */
    public static function getIntoApp()
    {
        $wapAppsRecientes = new WapAppsRecientes();

        if (self::validateIntoAppdata()) {
            $idUsuario = (int) $_POST['id_usuario'];
            $idApp = (int) $_POST['id_app'];
            $appReciente = $wapAppsRecientes->get(['id_usuario' => $idUsuario, 'id_app' => $idApp])->value;

            $now = date('Y-m-d H:i:s');
            /* Si Nunca ingreso a una App, generamos un registro */
            if (!$appReciente) {
                $wapAppsRecientes->set([
                    'id_usuario' => $idUsuario,
                    'ingresos' => 1,
                    'id_app' => $idApp,
                    'fecha_ingreso' => $now,
                    'fecha_referencia' => $now
                ]);
                $wapAppsRecientes->save();
            } else {
                $ingresos = (int) $appReciente['ingresos'];
                $params = [
                    'ingresos' => ++$ingresos,
                    'fecha_ingreso' => $now,
                    'fecha_referencia' => $now
                ];
                $wapAppsRecientes->update($params, $appReciente['id']);
            }
            $listadoApps = $wapAppsRecientes->list(['id_usuario' => $idUsuario], ['ODER BY' => 'ingresos'])->value;

            usort($listadoApps, function ($a, $b) {
                return (int) $a['ingresos'] < (int) $b['ingresos'];
            });

            if (!$listadoApps instanceof ErrorException) {
                sendRes(['appsRecientes' => $listadoApps]);
            } else {
                sendRes(null, 'Error al guardar el registro');
            }
        }
        exit;
    }

    /** Verifica en todas las apps que el usuario ingreso alguna vez 
     * ya paso un cierto tiempo para restar 1 ingreso */
    public static function checkIncomingApps()
    {
        if (isset($_POST['id_usuario'])) {
            $wapAppsRecientes = new WapAppsRecientes();
            $idUsuario = (int) $_POST['id_usuario'];
            $listadoApps = $wapAppsRecientes->list(['id_usuario' => $idUsuario])->value;

            /* Obtenemos las apps que ya pasaron 30 dias desde su ultimo ingreso */
            $oldApps = array_filter($listadoApps, function ($app) {
                return strtotime($app['fecha_referencia']) < strtotime('-30 days');
            });

            /* Restamos 1 ingreso a las apps que ya pasaron 30 dias */
            foreach ($oldApps as $app) {
                $ingresos = (int) $app['ingresos'];

                $strNuevaReferencia = strtotime($app['fecha_referencia']);
                $strNow = strtotime(date('Y-m-d'));
                $referencia = $app['fecha_referencia'];

                while ($strNuevaReferencia < $strNow && $ingresos != 0) {
                    $ingresos--;
                    $date = new DateTime($referencia);
                    $date->add(new DateInterval('P30D'));
                    $referencia = $date->format('Y-m-d');
                    $strNuevaReferencia = strtotime($referencia);
                }

                /* Cuando la fecha de referencia supera la fecha actual */
                if ($strNuevaReferencia > $strNow) {
                    $referencia = date('Y-m-d H:i:s');
                }

                $params = ['ingresos' => $ingresos, 'fecha_referencia' => $referencia];
                $wapAppsRecientes->update($params, $app['id']);
            }
        }
        $listadoApps = $wapAppsRecientes->list(['id_usuario' => $idUsuario])->value;

        usort($listadoApps, function ($a, $b) {
            return (int) $a['ingresos'] < (int) $b['ingresos'];
        });

        if (!$listadoApps instanceof ErrorException) {
            sendRes(['appsRecientes' => $listadoApps]);
        } else {
            sendRes(null, 'Error al guardar el registro');
        }

        exit;
    }

    private static function validateIntoAppdata()
    {
        if (isset($_POST['id_usuario']) && isset($_POST['id_app'])) {
            $idUsuario = (int) $_POST['id_usuario'];
            $idApp = (int) $_POST['id_app'];

            if ($idUsuario && $idApp) {
                return  true;
            }
        }

        return false;
    }
}
