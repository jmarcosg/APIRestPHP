<?php

namespace App\Controllers\LicenciaComercial;

use App\Models\LicenciaComercial\Lc_Solicitud;
use App\Models\LicenciaComercial\Lc_Rubro;
use App\Controllers\RenaperController;

use ErrorException;

class Lc_SolicitudController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_solicitud';
    }

    public static function index()
    {
        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Lc_Solicitud();

        $data = $data->list($_GET, $ops)->value;

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public static function get()
    {
        $data = new Lc_Solicitud();

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

            $rubro = new Lc_RubroController();
            $rubros = $rubro->index(['id_solicitud' => $data['id']]);

            $rubrosArray = [];
            foreach ($rubros as $r) {
                $rubrosArray[] = $r['nombre'];
            }

            $data['rubros'] = $rubrosArray;
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
        $_POST['estado'] = 'act';
        $data = new Lc_Solicitud();
        $data->set($_POST);
        $id = $data->save();

        if (!$id instanceof ErrorException) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, $id->getMessage(), $_GET);
        };
        exit;
    }

    public static function updateFirts($req, $id)
    {
        $data = new Lc_Solicitud();
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

    public function updateSec($req, $id)
    {
        $data = new Lc_Solicitud();

        $rubros = explode(",", $req['rubros']);
        unset($req['rubros']);

        $rubro = new Lc_RubroController();
        $rubro->deleteBySolicitudId($id);

        foreach ($rubros as $r) {
            $rubro = new Lc_Rubro();
            $rubro->set(['id_solicitud' => $id, 'nombre' => $r]);
            $rubro->save();
        }

        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Lc_Solicitud();
        return $data->delete($id);
    }
}
