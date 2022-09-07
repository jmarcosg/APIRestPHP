<?php

namespace App\Controllers\LicenciaComercial;

use App\Models\LicenciaComercial\Lc_SolicitudHistorial;

use App\Models\LicenciaComercial\Lc_Rubro;
use App\Models\LicenciaComercial\Lc_Documento;

use App\Controllers\RenaperController;
use App\Traits\LicenciaComercial\QuerysSql;
use ErrorException;

class Lc_SolicitudHistorialController
{
    use QuerysSql;

    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_solicitud';
    }

    public static function index()
    {
        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Lc_SolicitudHistorial;

        $data = $data->list($_GET, $ops)->value;

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public static function indexCatastro()
    {
        $solicitud = new Lc_SolicitudHistorial;;

        $sql = self::getSqlSolicitudes("estado = 'cat'");
        $data = $solicitud->executeSqlQuery($sql, false);
        $data = self::formatSolicitudDataArray($data);

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public static function getById()
    {
        $solicitud = new Lc_SolicitudHistorial;

        $id = $_GET['id'];
        $sql = self::getSqlSolicitudes("id = $id");
        $data = $solicitud->executeSqlQuery($sql, true);
        $data = self::formatSolicitudData($data);

        if ($data) {

            /* Si la solicitud tiene cargado un tercero, lo buscamos por renaper */
            if ($data['pertenece'] == 'tercero') {
                $rc = new RenaperController();
                $dni = $data["dni_tercero"];
                $tramite = $data["tramite_tercero"];
                $genero = $data["genero_tercero"];
                $data['dataTercero'] = $rc->getDataTramite($genero, $dni, $tramite);
            }

            /* Obtenemos los rubros cargados */
            $rubro = new Lc_RubroController();
            $rubros = $rubro->index(['id_solicitud' => $data['id']]);

            $rubrosArray = [];
            foreach ($rubros as $r) {
                $rubrosArray[] = $r['nombre'];
            }

            $data['rubros'] = $rubrosArray;

            /* Obtenemos los documentos de la tercera etapa */
            $documento = new Lc_DocumentoController();
            $documentos = $documento->getFilesUrl(['id_solicitud' => $data['id']]);
            $data['documentos'] = $documentos;
        }

        if (!$data instanceof ErrorException) {
            if ($data !== false) {
                sendRes($data);
            } else {
                sendRes(null, 'No se encontro la solicitud', $_GET);
            }
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };
        exit;
    }

    public static function get()
    {
        $data = new Lc_SolicitudHistorial;

        $data = $data->get($_GET)->value;

        if ($data) {

            /* Si la solicitud tiene cargado un tercero, lo buscamos por renaper */
            if ($data['pertenece'] == 'tercero') {
                $rc = new RenaperController();
                $dni = $data["dni_tercero"];
                $tramite = $data["tramite_tercero"];
                $genero = $data["genero_tercero"];
                $data['dataTercero'] = $rc->getDataTramite($genero, $dni, $tramite);
            }

            /* Obtenemos los rubros cargados */
            $rubro = new Lc_RubroController();
            $rubros = $rubro->index(['id_solicitud' => $data['id']]);

            $rubrosArray = [];
            foreach ($rubros as $r) {
                $rubrosArray[] = $r['nombre'];
            }

            $data['rubros'] = $rubrosArray;

            /* Obtenemos los documentos de la tercera etapa */
            $documento = new Lc_DocumentoController();
            $documentos = $documento->getFilesUrl(['id_solicitud' => $data['id']]);
            $data['documentos'] = $documentos;
        }

        if (!$data instanceof ErrorException) {
            if ($data !== false) {
                sendRes($data);
            } else {
                sendRes(null, 'No se encontro la solicitud', $_GET);
            }
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };
        exit;
    }

    public static function store()
    {
        /* Guardamos la solicitud */
        $_POST['estado'] = 'act';
        $data = new Lc_SolicitudHistorial;
        $data->set($_POST);
        $id = $data->save();

        /* Guardamos un registro de reserva para los documentos */
        $documento = new Lc_Documento();
        $documento->set(['id_solicitud' => $id]);
        $documento->save();

        if (!$id instanceof ErrorException) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, $id->getMessage(), $_GET);
        };
        exit;
    }

    public static function updateFirts($req, $id)
    {
        $data = new Lc_SolicitudHistorial;
        if ($req["pertenece"] == 'propia') {
            $req['id_wappersonas_tercero'] = null;
            $req['dni_tercero'] = null;
            $req['tramite_tercero'] = null;
            $req['genero_tercero'] = null;
        }
        $lc =  $data->update($req, $id);

        if (!$lc instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $lc->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function updateSec($req, $id)
    {
        $data = new Lc_SolicitudHistorial;

        $rubros = explode(",", $req['rubros']);
        unset($req['rubros']);

        $rubro = new Lc_RubroController();
        $rubro->deleteBySolicitudId($id);

        foreach ($rubros as $r) {
            $rubro = new Lc_Rubro();
            $rubro->set(['id_solicitud' => $id, 'nombre' => $r]);
            $rubro->save();
        }

        $data = $data->update($req, $id);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function updateThir($req, $id)
    {
        $data = new Lc_SolicitudHistorial;

        $data = $data->update($req, $id);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function catastroUpdate($req, $id)
    {
        $data = new Lc_SolicitudHistorial;

        $estado = $req['estado'];

        /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
        if ($estado == 'aprobado') {
            $req['estado'] = 'docs';
        }

        /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
        if ($estado == 'retornado') {
            $req['estado'] = 'act';

            $solicitud = $data->get(['id' => $id])->value;
            $solicitud['id_solicitud'] = $id;
            unset($solicitud['id']);
            unset($solicitud['deleted_at']);
            unset($solicitud['fecha_alta']);
            die();
        }


        /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
        if ($estado == 'rechazado') {
            $req['estado'] = 'cat_rechazo';
        }

        $data = $data->update($req, $id);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function setView($id)
    {
        $data = new Lc_SolicitudHistorial;

        $data = $data->update(['visto' => '1'], $id);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function getHistorialById($id)
    {
        $solicitud = new Lc_SolicitudHistorial();

        $sql = self::getSqlHistorial("id_solicitud = $id");
        $data = $solicitud->executeSqlQuery($sql, false);

        if ($data) {
            return $data;
        } else {
            return null;
        }
    }

    public static function getHistorialBySol($id)
    {
        $solicitud = new Lc_SolicitudHistorial();

        $sql = self::getSqlHistorial("id_solicitud = $id");
        $data = $solicitud->executeSqlQuery($sql, false);

        sendRes($data);
        exit;
    }

    public function delete($id)
    {
        $data = new Lc_SolicitudHistorial;
        return $data->delete($id);
    }
}
