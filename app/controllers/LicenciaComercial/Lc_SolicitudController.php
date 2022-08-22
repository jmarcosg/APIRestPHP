<?php

namespace App\Controllers\LicenciaComercial;

use App\Controllers\RenaperController;

use App\Models\LicenciaComercial\Lc_Solicitud;
use App\Models\LicenciaComercial\Lc_SolicitudHistorial;
use App\Models\LicenciaComercial\Lc_SolicitudRubro;
use App\Models\LicenciaComercial\Lc_Documento;

use App\Traits\LicenciaComercial\TemplateEmailSolicitud;
use App\Traits\LicenciaComercial\QuerysSql;
use ErrorException;

class Lc_SolicitudController
{
    use QuerysSql, TemplateEmailSolicitud;

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
            $rubro = new Lc_SolicitudRubroController();
            $data['rubros'] = $rubro->getRubrosBySolicitud($data['id']);

            /* Obtenemos los documentos de la tercera etapa */
            $data['documentos'] = self::getDocumentsData($data['id']);

            /* Obtenemos los documentos de la tercera etapa */
            $documento = new Lc_DocumentoController();
            $data['documentosSelect'] = $documento->getDocumentosBySolicitud($data['id']);
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

            /* ELIMINAR ESTA LINEA CUANDO SE TERMINE EL SISTEMA DE NOTI */
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
                $rubro = new Lc_SolicitudRubroController();
                $data['rubros'] = $rubro->getRubrosBySolicitud($data['id']);

                /* Obtenemos los documentos  */
                $data['documentos'] = self::getDocumentsData($data['id']);

                /* Obtenemos el hostorial */
                $data['historial'] = Lc_SolicitudHistorialController::getHistorial($data['id_usuario']);
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
        $_POST['ver_inicio'] = 0;
        $_POST['ver_catastro'] = 0;
        $_POST['ver_ambiental'] = 0;
        $_POST['ver_documentos'] = 0;

        if ($_POST['tipo_persona'] == 'juridica') {
            $_POST['pertenece'] == 'propio';
        }

        $data = new Lc_Solicitud();
        $data->set($_POST);
        $id = $data->save();

        $sql = self::getSqlSolicitudes("id = $id");
        $solicitud = $data->executeSqlQuery($sql, true);
        $solicitud = self::formatSolicitudData($solicitud);

        /* Guardamos un registro de reserva para los documentos */
        $documento = new Lc_Documento();
        $documento->saveInitDocuments($id, $solicitud);

        /* Obtenemos los documentos para que el usuario los cargue */
        $documentos = self::getDocumentsData($id);

        if (!$id instanceof ErrorException) {

            self::sendEmail($id, 'inicio', $solicitud);

            sendRes([
                'id' => $id,
                'documentos' => $documentos
            ]);
        } else {
            sendRes(null, $id->getMessage(), [$id]);
        };
        exit;
    }

    public static function datosPersonales($req, $id)
    {
        $data = new Lc_Solicitud();

        /* buscamos el tipo de documento que corresponde a un Poder */
        $doc = new Lc_Documento();
        $poder = $doc->get(['id_solicitud' => $id, 'id_tipo_documento' => 2])->value;
        $dni = $doc->get(['id_solicitud' => $id, 'id_tipo_documento' => 3])->value;

        if ($req["pertenece"] == 'propia') {
            $req['id_wappersonas_tercero'] = null;
            $req['dni_tercero'] = null;
            $req['tramite_tercero'] = null;
            $req['genero_tercero'] = null;

            if ($poder) {
                /* Si actualizo a propio y existe el documento lo borramos */
                $idDocumento = $poder['id'];
                $doc->delete($idDocumento);
            }

            if ($dni) {
                /* Si actualizo a propio y existe el documento lo borramos */
                $idDocumento = $dni['id'];
                $doc->delete($idDocumento);
            }
        }

        if ($req['pertenece'] == 'tercero' && !$poder) {
            /* Si actualizo a tercero pero no existe el documento */
            $params = ['id_solicitud' => $id, 'id_tipo_documento' => 2, 'verificado' => 0];
            $doc->set($params);
            $doc->save();
        }

        if ($req['pertenece'] == 'tercero' && !$dni) {
            /* Si actualizo a tercero pero no existe el documento */
            $params = ['id_solicitud' => $id, 'id_tipo_documento' => 3, 'verificado' => 0];
            $doc->set($params);
            $doc->save();
        }

        $lc =  $data->update($req, $id);

        /* Obtenemos los documentos  */
        $documentos = self::getDocumentsData($id);

        if (!$lc instanceof ErrorException) {
            sendRes([
                'id' => $id,
                'documentos' => $documentos
            ]);
        } else {
            sendRes(null, $lc->getMessage(), ['id' => $id]);
        };
        exit;
    }

    public static function actividad($req, $id)
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
        $rubros = explode(",", $req['rubros']);
        unset($req['rubros']);

        /* Borramos los rubros viejos */
        $rubro = new Lc_SolicitudRubroController();
        $rubro->deleteBySolicitudId($id);

        /* Actualizamos los nuevos rubros */
        $rubro = new Lc_SolicitudRubro();
        $rubro->set(['id_solicitud' => $id, 'codigo' => $rubros[0], 'principal' => 1]);
        $rubro->save();
        unset($rubros[0]);
        foreach ($rubros as $r) {
            $rubro->set(['id_solicitud' => $id, 'codigo' => $r]);
            $rubro->save();
        }
    }

    /**
     * Modulo Verificacion de rubros
     * Realiza Cambios en los rubros
     */
    public static function documentosUpdate($req, $id)
    {
        $documentos = explode(",", $req['documentos']);
        unset($req['documentos']);

        /* Borramos los documentos con id mayor a 10 */
        $documentoController = new Lc_DocumentoController();
        $documentoController->deleteBySolicitudId($id);

        /* Actualizamos los nuevos documentos */
        $documento = new Lc_Documento();
        foreach ($documentos as $d) {
            $documento->set(['id_solicitud' => $id, 'id_tipo_documento' => $d, 'verificado' => 0]);
            $documento->save();
        }
    }

    /**
     * Modulo Verificacion de rubros
     * Evalua la solicitud en funcion de los rubros / descripción actividad
     */
    public static function initVeriUpdate($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        if ($solicitud['estado'] == 'ver_inicio') {

            /* Guaramos el ID del admin para generar registro de auditoria */
            $admin =  $req['id_wappersonas_admin'];
            unset($req['id_wappersonas_admin']);

            $estado = $req['estado'];

            /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
            if ($estado == 'aprobado') {
                $req['estado'] = 'cat';
                $req['ver_inicio'] = '1';
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'act_retornado_inicio';
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'inicio_rechazado';
            }

            $data = $data->update($req, $id);

            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'verificacion_inicio', $admin, $estado);
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
                $req['estado'] = 'ver_amb';
                $req['ver_catastro'] = '1';
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'act_retornado_cat';
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'cat_rechazado';
            }

            $data = $data->update($req, $id);

            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'verificacion_catastro', $admin, $estado);
        } else {
            $data = new ErrorException('Esta solicitud ya no se encuentra en el area');
        }

        if (!$data instanceof ErrorException) {

            $data = new Lc_Solicitud();
            $sql = self::getSqlSolicitudes("id = $id");
            $solicitud = $data->executeSqlQuery($sql, true);
            $solicitud = self::formatSolicitudData($solicitud);

            if ($solicitud['ver_ambiental'] == 1) {
                if ($estado === 'aprobado') self::sendEmail($id, 'catastro_aprobado', $solicitud);
            }

            if ($estado === 'rechazado') self::sendEmail($id, 'catastro_rechazado', $solicitud);
            if ($estado === 'retornado') self::sendEmail($id, 'catastro_retornado', $solicitud);

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

        if ($solicitud['estado'] == 'ver_amb') {

            /* Guaramos el ID del admin para generar registro de auditoria */
            $admin =  $req['id_wappersonas_admin'];
            unset($req['id_wappersonas_admin']);

            $estado = $req['estado'];

            /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
            if ($estado == 'aprobado') {
                $req['estado'] = 'ver_rubros';
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
            self::setHistory($id, 'verificacion_ambiental', $admin, $estado);
        } else {
            $data = new ErrorException('Esta solicitud ya no se encuentra en el area');
        }

        if (!$data instanceof ErrorException) {

            $data = new Lc_Solicitud();
            $sql = self::getSqlSolicitudes("id = $id");
            $solicitud = $data->executeSqlQuery($sql, true);
            $solicitud = self::formatSolicitudData($solicitud);

            if ($solicitud['ver_catastro'] == 1) {
                if ($estado === 'aprobado') self::sendEmail($id, 'catastro_aprobado', $solicitud);
            }

            if ($estado === 'rechazado') self::sendEmail($id, 'ambiental_rechazado', $solicitud);
            if ($estado === 'retornado') self::sendEmail($id, 'ambiental_retornado', $solicitud);

            $_PUT['id'] = $id;
            $_PUT['estado'] = $estado;
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

        if ($solicitud['estado'] == 'ver_rubros') {

            /* Guaramos el ID del admin para generar registro de auditoria */
            $admin =  $req['id_wappersonas_admin'];
            unset($req['id_wappersonas_admin']);

            $estado = $req['estado'];
            if ($estado == 'aprobado' || $estado === 'retornado') {
                self::rubrosUpdate($req, $id);
                self::documentosUpdate($req, $id);
            }

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
            unset($req['rubros']);
            unset($req['documentos']);
            $data = $data->update($req, $id);

            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'verificacion_rubros', $admin, $estado);
        } else {
            $data = new ErrorException('Esta solicitud ya no se encuentra en el area');
        }

        if (!$data instanceof ErrorException) {

            $data = new Lc_Solicitud();
            $sql = self::getSqlSolicitudes("id = $id");
            $solicitud = $data->executeSqlQuery($sql, true);
            $solicitud = self::formatSolicitudData($solicitud);

            if ($estado == 'aprobado') self::sendEmail($id, 'rubros_aprobado', $solicitud);
            if ($estado == 'rechazado') self::sendEmail($id, 'rubros_rechazado', $solicitud);
            if ($estado == 'retornado') self::sendEmail($id, 'rubros_retornado', $solicitud);

            $_PUT['id'] = $id;
            $_PUT['estado'] = $estado;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };

        exit;
    }

    public static function documentacion($req, $id)
    {
        $data = new Lc_Solicitud();

        $data = $data->update($req, $id);

        if (!$data instanceof ErrorException) {

            $data = new Lc_Solicitud();
            $sql = self::getSqlSolicitudes("id = $id");
            $solicitud = $data->executeSqlQuery($sql, true);
            $solicitud = self::formatSolicitudData($solicitud);

            self::sendEmail($id, 'documentacion', $solicitud);
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /**
     * Modulo Verificacion de documentos
     * Evalua la solicitud en funcion de documentos
     */
    public static function documentosVeriUpdate($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        /* Para que no se pisen */
        if ($solicitud['estado'] == 'ver_doc') {
            /* Guaramos el ID del admin para generar registro de auditoria */
            $admin =  $req['id_wappersonas_admin'];
            unset($req['id_wappersonas_admin']);

            $estado = $req['estado'];

            /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
            if ($estado == 'aprobado') {
                $req['ver_documentos'] = '1';
                $req['estado'] = 'finalizado';
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'doc';
                $req['ver_documentos'] = '0';
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'doc_rechazado';
            }

            $data = $data->update($req, $id);

            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'verificador_documentos', $admin, $estado);
        } else {
            $data = new ErrorException('Esta solicitud ya no se encuentra en el area');
        }

        if (!$data instanceof ErrorException) {

            $data = new Lc_Solicitud();
            $sql = self::getSqlSolicitudes("id = $id");
            $solicitud = $data->executeSqlQuery($sql, true);
            $solicitud = self::formatSolicitudData($solicitud);

            if ($estado === 'aprobado') self::sendEmail($id, 'documentos_aprobado', $solicitud);
            if ($estado === 'rechazado') self::sendEmail($id, 'documentos_rechazado', $solicitud);
            if ($estado === 'retornado') self::sendEmail($id, 'documentos_retornado', $solicitud);

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
    public static function evalDocumento($req, $id)
    {
        $idSolicitud = $req['id_solicitud'];
        $tipoDocumento = $req['tipo_documento'];
        $sql = "SELECT * FROM lc_documentos WHERE id_solicitud = $idSolicitud AND id_tipo_documento = $tipoDocumento";

        $data = new Lc_Documento();
        $documento = $data->executeSqlQuery($sql);

        $params = ['verificado' => $req['estado'], 'id_wap_personas_admin' => $req['id_wappersonas_admin']];
        $result = $data->update($params, $documento['id']);

        if ($result) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, 'No se pudo actualizar el estado del documento', ['id' => $id]);
        }
        exit;
    }

    /**
     * Genera un registro de historial de la solicitud
     * @param String $id      Id de la solicitud
     * @param String $tipo    Tipo de historial
     * @param String $admin   Id del admin que genera el historial 
     * @return void 
     */
    private static function setHistory($id, $tipo, $admin, $estado)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        $solicitud['id_solicitud'] = $id;
        $solicitud['id_wappersonas_admin'] = $admin;
        $solicitud['estado'] = $estado;
        $solicitud['tipo_registro'] = $tipo;
        $solicitud['visto'] = 0;

        $solhistorial = new Lc_SolicitudHistorial();
        $solhistorial->set($solicitud);
        $idSolHistorial = $solhistorial->save();

        $rubro = new Lc_SolicitudRubroController();
        $rubros = $rubro->index(['id_solicitud' => $id]);

        foreach ($rubros as $r) {
            $r['id_solicitud_historial'] = $idSolHistorial;
            $r['id_solicitud'] = null;
            $rubro = new Lc_SolicitudRubro();
            $rubro->set($r);
            $rubro->save();
        }
    }

    private static function getDocumentsData($id)
    {
        $documentoController = new Lc_DocumentoController();
        return $documentoController->getFilesUrl(['id_solicitud' => $id]);
    }

    public function delete($id)
    {
        $data = new Lc_Solicitud();
        return $data->delete($id);
    }
}
