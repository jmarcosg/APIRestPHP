<?php

namespace App\Controllers\Arbolado;

use App\Connections\BaseDatos;
use App\Controllers\RenaperController;
use App\Models\Arbolado\Arb_Audit;
use App\Models\Arbolado\Arb_Podador;
use App\Traits\Arbolado\TemplateEmailPodador;

use App\Models\Arbolado\MYPDF;

use DateInterval;
use DateTime;
use ErrorException;

class Arb_PodadorController
{
    use TemplateEmailPodador;

    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_Podador';
    }

    public static function index()
    {

        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Podador();

        $data = $data->list($_GET, $ops)->value;

        /* Forzamos estado deshabilitado */
        foreach ($data as $key => $el) {
            if (self::esDeshabilitado($el)) {
                $data[$key]['estado'] = 'deshabilitado';
            }
        }

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    public static function get()
    {
        $data = new Arb_Podador();

        $data = $data->get($_GET)->value;

        if ($data['estado'] == 'aprobado' && !self::esDeshabilitado($data)) {
            $genero = $data["wapPersona"]["Genero"];
            $dni = $data["wapPersona"]["Documento"];

            $renaper = new RenaperController();
            $img = $renaper->getImage($genero, $dni);

            $img['qr'] = self::getCodigoQr($data['id']);
            $data['img'] = $img;
        }

        if (self::esDeshabilitado($data)) {
            $data['estado'] = 'deshabilitado';
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
        /* Logica para el certificado */
        $file = $_FILES['certificado'];
        $nameFile = uniqid() . getExtFile($file);

        $_POST['certificado'] = $nameFile;

        /* Guardamos la solicitud */
        $_POST['estado'] = 'nuevo';
        $data = new Arb_Podador();
        $data->set($_POST);
        $id = $data->save();

        /* copiamos el archivo en la carpeta correspondiente */
        $path = getPathFile($file, FILE_PATH_LOCAL . "arbolado/podador/$id/", $nameFile);
        $copiado = copy($file['tmp_name'], $path);

        if ($id instanceof ErrorException || !$copiado) {
            self::delete($id);
            sendRes(null, $id->getMessage(), $_GET);
            exit;
        }

        /* Envio de correo electronico */
        $email = ['email' => $_POST['email']];
        self::sendEmail($id, 'envio', $email);

        sendRes(['id' => $id]);
        exit;
    }

    public static function update($id)
    {
        /* Formateo de la información */
        parse_str(file_get_contents('php://input'), $_PUT);

        if ($_PUT["motivo_deshabilitado"] != "null") {
            $typeSendEmail = 'deshabilitado';
            $obsSendEmail = $_PUT['motivo_deshabilitado'];
            $_PUT['fecha_deshabilitado'] = date("Y-m-d", strtotime(date('Y-m-d') . "+ 1 year"));
        } else {
            $typeSendEmail = $_PUT['estado'];
            $_PUT["motivo_deshabilitado"] = null;
            $_PUT["fecha_deshabilitado"] = null;
            $obsSendEmail = $_PUT['observacion'];
        }

        $email = $_PUT['email'];
        unset($_PUT['email']);

        /* Actualizamos la solicitud */
        $data = new Arb_Podador();

        $now = new DateTime();

        $_PUT['fecha_revision'] = $now->format('Y-m-d');

        /* En el caso que deshabiliten al podador para no borrar la observacion actual */
        if ($_PUT["observacion"] == "") unset($_PUT["observacion"]);

        if ($_PUT['estado'] == 'aprobado') {
            /* Establecemos la fecha de vencimiento */
            $interval = new DateInterval('P2Y');
            $now->add($interval);
            $_PUT['fecha_vencimiento'] = $now->format('Y-m-d');

            /* Obtenemos la ultima evalacion del usuario */
            $params = ['id_wappersonas' => $_PUT['id_wappersonas'], 'TOP' => 1];
            $op = ['order' => ' ORDER BY id DESC '];
            $arbEvaluacionController = new Arb_EvaluacionController();
            $evaluacion = $arbEvaluacionController->index($params, $op);
            $idEvalacion = $evaluacion[0]['id'];

            /* Actualizamos la evaluacion con el id de la solicitud */
            $evaluacion = $arbEvaluacionController->update(['id_podador' => $id], $idEvalacion);
        }
        unset($_PUT['id_wappersonas']);

        /* Generamos registro para la auditoria */
        $audit = new Arb_Audit();
        $accion = $_PUT["motivo_deshabilitado"] == null ? $_PUT['estado'] : 'deshabilitacion';
        $audit->set([
            'id_usuario' => $_PUT['id_usuario_admin'],
            'id_wappersonas' => $_PUT['id_wappersonas_admin'],
            'id_podador' => $id,
            'accion' => $accion,
            'observacion' => $_PUT['motivo_deshabilitado'],
        ]);
        $audit->save();

        $solicitud = $data->update($_PUT, $id);

        /* Enviamos el correo electronico */
        $data = [
            'email' => $email,
            'observacion' =>  $obsSendEmail,
        ];

        self::sendEmail($id, $typeSendEmail, $data);

        if (!$solicitud instanceof ErrorException) {
            $_PUT['id'] = $id;
            if ($_PUT['fecha_deshabilitado'] > date('Y-m-d')) {
                $_PUT['estado'] = 'deshabilitado';
            }
            sendRes($_PUT);
        } else {
            sendRes(
                null,
                $solicitud->getMessage(),
                ['id' => $id]
            );
        };
        exit;
    }

    public static function delete($id)
    {
        $data = new Arb_Podador();
        $data = $data->delete($id);

        if (!$data instanceof ErrorException) {
            sendRes(['id' => $id]);
        } else {
            sendRes(null, $data->getMessage(), ['id' => $id]);
        };
        exit;
    }

    /** Obtenemos todos los aprobados para el front*/
    public static function getAprobados()
    {
        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Podador();

        $data = $data->list($_GET, $ops)->value;

        /* Filtramos las que no se encuentran deshabilitados */
        $data = array_filter($data, function ($el) {
            return !self::esDeshabilitado($el);
        });

        $data = array_values($data);

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    /** Obtenemos todos los aprobados para el front*/
    public static function getAprobadosPdf()
    {
        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Podador();

        $data = $data->list($_GET, $ops)->value;

        /* Filtramos las que no se encuentran deshabilitados */
        $data = array_filter($data, function ($el) {
            return !self::esDeshabilitado($el);
        });

        $data = array_values($data);

        if (!$data instanceof ErrorException) {
            return $data;
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };

        exit;
    }

    /** Obtenemos todos los deshabilitados */
    public static function getDeshabilitados()
    {
        $ops = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Podador();

        $data = $data->list($_GET, $ops)->value;

        /* Filtramos las que no se encuentran deshabilitados */
        $data = array_filter($data, function ($el) {
            return self::esDeshabilitado($el);
        });

        /* Forzamos estado deshabilitado */
        foreach ($data as $key => $el) {
            $data[$key]['estado'] = 'deshabilitado';
        }

        if (!$data instanceof ErrorException) {
            sendRes($data);
        } else {
            sendRes(null, $data->getMessage(), $_GET);
        };
        exit;
    }

    public function existeSol($id_usuario)
    {
        $params = ['id_usuario' => $id_usuario, 'estado' => 'nuevo', 'TOP' => 1];
        $op = ['order' => ' ORDER BY id DESC '];

        $solicitud = $this->index($params, $op);

        if ($solicitud) {
            return $solicitud[0];
        }

        return $solicitud;
    }

    public static function getEstadoSolicitudDetalle()
    {
        $params = ['id_wappersonas' => $_GET['id_wappersonas'], 'TOP' => 1];

        $op = ['order' => ' ORDER BY id DESC '];

        $data = new Arb_Podador();

        $solicitud = $data->list($params, $op)->value;

        $response = [
            'estado' => null,
            'msg' => null,
        ];

        if ($solicitud) {
            $solicitud = $solicitud[0];
            $id = $solicitud['id'];
            $venc = $solicitud['fecha_vencimiento'];

            if ($solicitud['estado'] == 'nuevo') {
                $response = [
                    'estado' => 'nuevo',
                    'msg' => "La solcitud: $id, se encuentra en proceso de revisión",
                ];
            }

            if ($solicitud['estado'] == 'rechazado') {
                $observacion = $solicitud['observacion'];
                $response = [
                    'estado' => 'rechazado',
                    'msg' => "Solicitud Número: $id fue rechazada. $observacion",
                ];
            }

            if ($solicitud['estado'] == 'aprobado') {
                if (!esVigente($venc)) {
                    $venc = date("d/m/Y", strtotime($venc));
                    $response = [
                        'estado' => 'vencida',
                        'msg' => "Solicitud Número: $id vencida con fecha $venc",
                    ];
                } else {
                    $venc = date("d/m/Y", strtotime($venc));
                    $response = [
                        'estado' => 'vigente',
                        'msg' => "El carnet se encuentra vigente hasta la fecha: $venc",
                    ];
                }
            }
        }

        sendRes($response);
        exit;
    }

    public function getDatosCarnet($id)
    {
        $sql =
            "SELECT 
                id,
                wap_per.Nombre,
                wap_per.Documento,
                certificado,
                estado,
                fecha_vencimiento,
                fecha_revision,
                genero
            FROM dbo.arb_podadores arb_pod
            LEFT JOIN dbo.wapPersonas wap_per ON arb_pod.id_wappersonas  = wap_per.ReferenciaID
            WHERE id = $id";

        $conn = new BaseDatos();
        $query =  $conn->query($sql);

        return odbc_fetch_array($query);
    }

    public static function getCodigoQr($idSolicitud)
    {
        if (PROD) {
            $baseUrl = "https://weblogin.muninqn.gov.ar/apps/APIRest/public/views/arbolado/infoPodador.php?numero=";
        } else {
            $baseUrl = "http://200.85.183.194:90/apps/APIRest/public/views/arbolado/infoPodador.php?numero=";
        }
        $url = "https://chart.googleapis.com/chart?chs=250x250&chco=006BB1&cht=qr&chl=" . $baseUrl . $idSolicitud;
        $imagen = base64_encode(file_get_contents($url));
        return "data:image/png;base64," . $imagen;
    }

    private static function esDeshabilitado($data)
    {
        return $data["fecha_deshabilitado"] > date('Y-m-d');
    }

    public function getPodadoresPdf()
    {
        $params = ['estado' => 'aprobado', 'TOP' => 10000];
        $podadores = $this->getAprobadosPdf($params, ['order' => ' ORDER BY id DESC ']);
        $header = array('Nro', 'DNI', 'NOMBRE', 'TELEFONO', 'INFO');

        /* Filtramos los no vencidos */
        $podadores = array_filter($podadores, function ($el) {
            return $el['fecha_vencimiento'] > date('Y-m-d') || $el['fecha_vencimiento'] == null;
        });

        $data = [];
        foreach ($podadores as $p) {
            $data[] = [
                $p['id'],
                $p['wapPersona']['Documento'],
                $p['wapPersona']['Nombre'],
                $p['telefono'],
                $p['observacion'],
            ];
        }

        // create new PDF documentgetPodadoresPdf
        $pdf = new MYPDF('P', 'mm');

        // set document information
        $pdf->SetCreator('Municipalidad de Neuquén');
        $pdf->SetAuthor('Municipalidad de Neuquén');
        $pdf->SetTitle('Listado Podadores - ' . date('d/m/Y'));
        $pdf->SetSubject('Listado Podadores');
        $pdf->SetKeywords('Listado Podadores');

        // add a page
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->Text(15, 30, 'LISTADO PODADORES');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Image('https://weblogin.muninqn.gov.ar/apps/estilos_globales/logo-credencial.png', 100, 13, 97.3, 14, 'PNG');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Text(110, 30, 'SECRETARIA DE MOVILIDAD Y SERVICIOS AL CIUDADANO');
        $pdf->Text(123, 34, 'SUBSECRETARIA DE ESPACIOS VERDES');
        $pdf->Ln(10);
        // print colored table
        $pdf->ColoredTable($header, utf8ize($data));

        // close and output PDF document
        $pdf->Output(ADJUNTOS_PATH . '\Listado_podadoresssss.pdf', 'F');
        $pdf->Output('Listado_podadores.pdf', 'I');
    }
}
