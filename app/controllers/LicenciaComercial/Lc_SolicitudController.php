<?php

namespace App\Controllers\LicenciaComercial;

use App\Controllers\RenaperController;

use App\Models\LicenciaComercial\Lc_Solicitud;
use App\Models\LicenciaComercial\Lc_SolicitudHistorial;
use App\Models\LicenciaComercial\Lc_SolicitudRubro;
use App\Models\LicenciaComercial\Lc_Documento;

use App\Traits\LicenciaComercial\TemplateEmailSolicitud;
use App\Traits\LicenciaComercial\QuerysSql;
use App\Traits\LicenciaComercial\Reportes;
use ErrorException;

class Lc_SolicitudController
{
    use QuerysSql, TemplateEmailSolicitud, Reportes;

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
        $id = $_GET['id'];
        $data = self::getSolicitudByQuery("id = $id");

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

            /* Obtenemos las notas de catastro y ambiente */
            $data['notas_catastro'] = self::getNota($data, 'notas_catastro');
            $data['notas_ambiente'] = self::getNota($data, 'notas_ambiente');

            $data['historial'] = Lc_SolicitudHistorialController::getHistorialById($id);
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

    public static function indexById()
    {
        $id = $_GET['id_usuario'];
        $data = self::getSolicitudByQuery("id_usuario = $id", false);

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
                $data['historial'] = Lc_SolicitudHistorialController::getHistorialById($data['id']);
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
            $_POST['pertenece'] = 'propia';
        }

        $data = new Lc_Solicitud();
        $data->set($_POST);
        $id = $data->save();

        $solicitud =  self::getSolicitudByQuery("id = $id");

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
        $savedSolicitud = $data->get(['id' => $id])->value;

        /* buscamos el tipo de documento que corresponde a un Poder */
        $doc = new Lc_Documento();

        if ($savedSolicitud['tipo_persona'] != $req['tipo_persona'] || $savedSolicitud['pertenece'] != $req['pertenece']) {
            $doc->deleteInitDocuments($id);
            $doc->saveInitDocuments($id, $req);
        }

        $solicitud =  $data->update($req, $id);

        /* Obtenemos los documentos  */
        $documentos = self::getDocumentsData($id);

        if (!$solicitud instanceof ErrorException) {
            sendRes([
                'id' => $id,
                'documentos' => $documentos
            ]);
        } else {
            sendRes(null, $solicitud->getMessage(), ['id' => $id]);
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
                self::sendEmail($id, 'inicio_aprobado', $solicitud);
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'act_retornado_inicio';
                self::sendEmail($id, 'inicio_retornado', $solicitud);
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'inicio_rechazado';
                self::sendEmail($id, 'inicio_rechazado', $solicitud);
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
                self::sendEmail($id, 'catastro_aprobado', $solicitud);
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'act_retornado_cat';
                self::sendEmail($id, 'catastro_retornado', $solicitud);
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'cat_rechazado';
                self::sendEmail($id, 'catastro_rechazado', $solicitud);
            }

            $data = $data->update($req, $id);

            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'verificacion_catastro', $admin, $estado);
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

        if ($solicitud['estado'] == 'ver_amb') {

            /* Guaramos el ID del admin para generar registro de auditoria */
            $admin =  $req['id_wappersonas_admin'];
            unset($req['id_wappersonas_admin']);

            $estado = $req['estado'];

            /* Cuando llega aprobado, actualizamos la obs, y lo enviamos a docs */
            if ($estado == 'aprobado') {
                $req['estado'] = 'ver_rubros';
                $req['ver_ambiental'] = '1';

                self::documentosUpdate($req, $id);
                self::sendEmail($id, 'ambiental_aprobado', $solicitud);
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'ver_rubros';
                $req['ver_rubros'] = '0';
            }

            /* Cuando llega rechazado, actualizamos la obs, hacemos que el usuario genere una nueva solicitud */
            if ($estado == 'rechazado') {
                $req['estado'] = 'ambiental_rechazado';
                self::sendEmail($id, 'ambiental_rechazado', $solicitud);
            }

            unset($req['documentos']);
            $data = $data->update($req, $id);

            /* Registramos un historial de la solicitud  */
            self::setHistory($id, 'verificacion_ambiental', $admin, $estado);
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
            if ($estado == 'aprobado') {
                if ($req["documentos"] == "") {
                    $req['estado'] = 'ver_doc';
                } else {
                    $req['estado'] = 'doc';
                }
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

            $solicitud =  self::getSolicitudByQuery("id = $id");

            if ($estado == 'aprobado') self::sendEmail($id, 'rubros_aprobado', $solicitud);
            if ($estado == 'rechazado') self::sendEmail($id, 'rubros_rechazado', $solicitud);

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
            $solicitud =  self::getSolicitudByQuery("id = $id");
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
                $req['fecha_finalizado'] = date('Y-m-d H:i:s');
                $req['estado'] = 'finalizado';
            }

            /* Cuando llega retornado, actualizamos la obs, generamos un registro clon de la solicitud */
            if ($estado == 'retornado') {
                $req['estado'] = 'doc_retornado_documentos';
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

            $solicitud =  self::getSolicitudByQuery("id = $id");

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
     * Modulo Auditoria
     * Ingresa un número de expediente a la solicitud
     */
    public static function setExpediente($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        $admin =  $req['id_wappersonas_admin'];
        unset($req['id_wappersonas_admin']);

        $data = $data->update($req, $id);

        /* Registramos un historial de la solicitud  */
        self::setHistory($id, 'ingreso_expediente', $admin, $solicitud['estado']);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
            sendRes($_PUT);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /**
     * Modulo Auditoria
     * Ingresa un número de licencia comercial a la solicitud
     */
    public static function setLicenciaComercial($req, $id)
    {
        $data = new Lc_Solicitud();
        $solicitud = $data->get(['id' => $id])->value;

        $admin =  $req['id_wappersonas_admin'];
        unset($req['id_wappersonas_admin']);

        $data = $data->update($req, $id);

        /* Registramos un historial de la solicitud  */
        self::setHistory($id, 'ingreso_licencia_comercial', $admin, $solicitud['estado']);

        if (!$data instanceof ErrorException) {
            $_PUT['id'] = $id;
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

    private static function getSolicitudByQuery($query, $fetch = true)
    {
        $solicitud = new Lc_Solicitud();

        $sql = self::getSqlSolicitudes($query);
        $data = $solicitud->executeSqlQuery($sql, $fetch);

        if ($fetch) {
            return self::formatSolicitudData($data);
        } else {
            return self::formatSolicitudDataArray($data);
        }
    }

    private static function getNota($data, $nota)
    {
        $id = $data['id'];
        if ($data[$nota]) {
            $documento = new Lc_Documento();
            $filesUrl = $documento->filesUrl;
            $url = $filesUrl . $id . "/" . $nota  . '/' .  $data[$nota];
            return [
                'url' => getBase64String($url, $data[$nota]),
                'loading' =>  false,
                'error' => false
            ];
        }
        return null;
    }
}
