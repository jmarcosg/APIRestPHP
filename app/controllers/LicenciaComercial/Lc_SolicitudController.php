<?php

namespace App\Controllers\LicenciaComercial;

use App\Models\LicenciaComercial\Lc_Solicitud;
use App\Models\LicenciaComercial\Lc_SolicitudHistorial;
use App\Models\LicenciaComercial\Lc_Rubro;
use App\Models\LicenciaComercial\Lc_Documento;

use App\Controllers\RenaperController;
use App\Traits\LicenciaComercial\QuerysSql;
use ErrorException;

class Lc_SolicitudController
{
    use QuerysSql;

    public function __construct()
    {
        $GLOBALS['exect'][] = 'lc_solicitud';
    }

    public static function index($where)
    {
        $solicitud = new Lc_Solicitud();

        $sql = self::getSqlSolicitudes($where);
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
        $solicitud = new Lc_Solicitud();

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
        $data = new Lc_Solicitud();

        $ops = ['order' => ' ORDER BY id DESC '];
        $_GET['TOP'] = 1;
        $data = $data->list($_GET, $ops)->value;

        if (count($data) > 0) {
            $data = $data[0];

            if (str_contains($data['estado'], 'rechazado')) $data = false;

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

                /* Obtenemos los documentos  */
                $documento = new Lc_DocumentoController();
                $documentos = $documento->getFilesUrl(['id_solicitud' => $data['id']]);
                $data['documentos'] = $documentos;
            }
        } else {
            $data = false;
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

        $_POST['tiene_local'] = 1;
        $_POST['ver_rubros'] = 0;
        $_POST['ver_catastro'] = 0;
        $_POST['ver_ambiental'] = 0;
        $_POST['ver_documentos'] = 0;

        $data = new Lc_Solicitud();
        $data->set($_POST);
        $id = $data->save();

        /* Guardamos un registro de reserva para los documentos */
        $documento = new Lc_Documento();
        $documento->saveInitDocuments($id);

        $_GET['id_usuario'] = $_POST['id_usuario'];
        $solicitud = self::get();

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

    public static function updateSec($req, $id)
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
        $data = new Lc_Solicitud();

        $data = $data->update($req, $id);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /**
     * Modulo Verificacion de rubros
     * Realiza Cambios en los rubros
     */
    public static function rubrosUpdate($req, $id)
    {
        $data = new Lc_Solicitud();

        /* Actualizamos la observacion por el cambio de rubro */
        $obsRubros = $req['observacion_rubros'];
        $data->update(['observacion_rubros' => $obsRubros], $id);

        /* Generamos una replica en el historico */
        $admin =  $req['id_wappersonas_admin'];
        self::setHistory($id, 'cambio_rubros', $admin);

        $rubros = explode(",", $req['rubros']);
        unset($req['rubros']);

        /* Borramos los rubros viejos */
        $rubro = new Lc_RubroController();
        $rubro->deleteBySolicitudId($id);

        /* Actualizamos los nuevos rubros */
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

    /**
     * Modulo Verificacion de rubros
     * Evalua la solicitud en funcion de los rubros / descripción actividad
     */
    public static function rubrosVeriUpdate($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        /* Guaramos el ID del admin para generar registro de auditoria */
        $admin =  $req['id_wappersonas_admin'];
        unset($req['id_wappersonas_admin']);

        $estado = $req['estado'];
        /* Si se aprueba y no tiene local lo mandamos a pedir los archivos */
        if ($estado == 'aprobado' && $solicitud['tiene_local'] === '0') {
            $req['estado'] = 'doc';
            $req['ver_rubros'] = '1';
        }

        /* Si se aprueba y tiene local lo mandamos a catastro */
        if ($estado == 'aprobado' && $solicitud['tiene_local'] === '1') {
            $req['estado'] = 'cat';
            $req['ver_rubros'] = '1';
        }

        /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
        if ($estado == 'retornado') {
            $req['estado'] = 'act_retornado';
        }

        /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
        if ($estado == 'rechazado') {
            $req['estado'] = 'rubros_rechazado';
        }

        /* Guardamos la solcitidu */
        $data = $data->update($req, $id);

        /* Registramos un historial de la solicitud  */
        self::setHistory($id, 'rubros_verificador', $admin);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            $_PUT['estado'] = $estado;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /**
     * Modulo Catastro
     * Evalua la solicitud en funcion de los rubros / nomenclatura
     */
    public static function catastroVeriUpdate($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        /* Para que no se pisen entre Verificacion ambiental y catastro */
        if ($solicitud['estado'] == 'cat') {
            /* Guaramos el ID del admin para generar registro de auditoria */
            $admin =  $req['id_wappersonas_admin'];
            unset($req['id_wappersonas_admin']);

            $estado = $req['estado'];

            /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
            if ($estado == 'aprobado') {
                if ($solicitud['ver_ambiental'] === '1') {
                    $req['estado'] = 'doc';
                } else {
                    $req['estado'] = 'cat';
                }
                $req['ver_catastro'] = '1';
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'ver_rubros';
                $req['ver_rubros'] = '0';
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'cat_rechazado';
            }

            $data = $data->update($req, $id);


            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'catastro', $admin);
        } else {
            $data = new ErrorException('Esta solicitud ya no se encuentra en el area');
        }


        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            $_PUT['estado'] = $estado;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /**
     * Modulo Catastro - Verificación Ambiental
     * Evalua la solicitud en funcion de los rubros / nomenclatura
     */
    public static function ambientalVeriUpdate($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        /* Guaramos el ID del admin para generar registro de auditoria */
        $admin =  $req['id_wappersonas_admin'];
        unset($req['id_wappersonas_admin']);

        $estado = $req['estado'];

        /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
        if ($estado == 'aprobado') {
            if ($solicitud['ver_catastro'] === '1') {
                $req['estado'] = 'doc';
            } else {
                $req['estado'] = 'cat';
            }
            $req['ver_ambiental'] = '1';
        }

        /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
        if ($estado == 'retornado') {
            $req['estado'] = 'ver_rubros';
            $req['ver_rubros'] = '0';
        }

        /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
        if ($estado == 'rechazado') {
            $req['estado'] = 'ambiental_rechazado';
        }

        $data = $data->update($req, $id);

        /* Registramos un historial de la solicitud  */
        self::setHistory($id, 'verificacion_ambiental', $admin);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            $_PUT['estado'] = $estado;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /**
     * Genera un registro de historial de la solicitud
     * @param String $id      Id de la solicitud
     * @param String $tipo    Tipo de historial
     * @param String $admin   Id del admin que genera el historial 
     * @return void 
     */
    private static function setHistory($id, $tipo, $admin)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        $solicitud['id_solicitud'] = $id;
        $solicitud['tipo_registro'] = $tipo;
        $solicitud['id_wappersonas_admin'] = $admin;

        $solhistorial = new Lc_SolicitudHistorial();
        $solhistorial->set($solicitud);
        $idSolHistorial = $solhistorial->save();

        $rubro = new Lc_RubroController();
        $rubros = $rubro->index(['id_solicitud' => $id]);

        foreach ($rubros as $r) {
            $r['id_solicitud_historial'] = $idSolHistorial;
            $r['id_solicitud'] = null;
            $rubro = new Lc_Rubro();
            $rubro->set($r);
            $rubro->save();
        }
    }

    public function delete($id)
    {
        $data = new Lc_Solicitud();
        return $data->delete($id);
    }
}
