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

class Arb_PodadorController
{
    use TemplateEmailPodador;

    public function __construct()
    {
        $GLOBALS['exect'][] = 'arb_Podador';
    }

    public function index($param = [], $ops = [])
    {
        $data = new Arb_Podador();
        $data = $data->list($param, $ops)->value;

        /* Forzamos estado deshabilitado */
        foreach ($data as $key => $el) {
            if ($this->esDeshabilitado($el)) {
                $data[$key]['estado'] = 'deshabilitado';
            }
        }

        return $data;
    }

    /** Obtenemos los no deshabilitados */
    public function getNoDeshabilitados($param = [], $ops = [])
    {
        $data = new Arb_Podador();
        $data = $data->list($param, $ops)->value;

        /* Filtramos las que no se encuentran deshabilitados */
        $data = array_filter($data, function ($el) {
            return !$this->esDeshabilitado($el);
        });

        return $data;
    }

    /** Obtenemos todos los deshabilitados */
    public function getDeshabilitados($param = [], $ops = [])
    {
        $data = new Arb_Podador();
        $data = $data->list($param, $ops)->value;

        /* Filtramos las que no se encuentran deshabilitados */
        $data = array_filter($data, function ($el) {
            return $this->esDeshabilitado($el);
        });

        /* Forzamos estado deshabilitado */
        foreach ($data as $key => $el) {
            $data[$key]['estado'] = 'deshabilitado';
        }

        return $data;
    }

    public function get($params)
    {
        $data = new Arb_Podador();
        $data = $data->get($params)->value;

        if ($data['estado'] == 'aprobado' && !$this->esDeshabilitado($data)) {
            $genero = $data["wapPersona"]["Genero"];
            $dni = $data["wapPersona"]["Documento"];

            $renaper = new RenaperController();
            $img = $renaper->getImage($genero, $dni);

            $img['qr'] = $this->getCodigoQr($data['id']);
            $data['img'] = $img;
        }

        if ($this->esDeshabilitado($data)) {
            $data['estado'] = 'deshabilitado';
        }

        return $data;
    }

    public function store($res)
    {
        $res['estado'] = 'nuevo';
        $data = new Arb_Podador();
        $data->set($res);
        return $data->save();
    }

    public function update($req, $id)
    {
        $data = new Arb_Podador();

        $now = new DateTime();

        $req['fecha_revision'] = $now->format('Y-m-d');

        /* En el caso que deshabiliten al podador para no borrar la observacion actual */
        if ($req["observacion"] == "") unset($req["observacion"]);

        if ($req['estado'] == 'aprobado') {
            /* Establecemos la fecha de vencimiento */
            $interval = new DateInterval('P2Y');
            $now->add($interval);
            $req['fecha_vencimiento'] = $now->format('Y-m-d');

            /* Obtenemos la ultima evalacion del usuario */
            $params = ['id_wappersonas' => $req['id_wappersonas'], 'TOP' => 1];
            $op = ['order' => ' ORDER BY id DESC '];
            $arbEvaluacionController = new Arb_EvaluacionController();
            $evaluacion = $arbEvaluacionController->index($params, $op);
            $idEvalacion = $evaluacion[0]['id'];

            /* Actualizamos la evaluacion con el id de la solicitud */
            $evaluacion = $arbEvaluacionController->update(['id_podador' => $id], $idEvalacion);
        }
        unset($req['id_wappersonas']);

        /* Generamos registro para la auditoria */
        $audit = new Arb_Audit();
        $accion = $req["motivo_deshabilitado"] == null ? $req['estado'] : 'deshabilitacion';
        $audit->set([
            'id_usuario' => $req['id_usuario_admin'],
            'id_wappersonas' => $req['id_wappersonas_admin'],
            'id_podador' => $id,
            'accion' => $accion,
            'observacion' => $req['motivo_deshabilitado'],
        ]);
        $audit->save();

        return $data->update($req, $id);
    }

    public function delete($id)
    {
        $data = new Arb_Podador();
        return $data->delete($id);
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

    public function getEstadoSolicitudDetalle($id_wappersonas)
    {
        $params = ['id_wappersonas' => $id_wappersonas, 'TOP' => 1];
        $op = ['order' => ' ORDER BY id DESC '];

        $solicitud = $this->index($params, $op);

        if ($solicitud) {
            $solicitud = $solicitud[0];
            $id = $solicitud['id'];
            $venc = $solicitud['fecha_vencimiento'];

            if ($solicitud['estado'] == 'nuevo') {
                return [
                    'estado' => 'nuevo',
                    'msg' => "La solcitud: $id, se encuentra en proceso de revisión",
                ];
            }

            if ($solicitud['estado'] == 'rechazado') {
                $observacion = $solicitud['observacion'];
                return [
                    'estado' => 'rechazado',
                    'msg' => "Solicitud Número: $id fue rechazada. $observacion",
                ];
            }

            if ($solicitud['estado'] == 'aprobado') {
                if (!esVigente($venc)) {
                    $venc = date("d/m/Y", strtotime($venc));
                    return [
                        'estado' => 'vencida',
                        'msg' => "Solicitud Número: $id vencida con fecha $venc",
                    ];
                } else {
                    $venc = date("d/m/Y", strtotime($venc));
                    return [
                        'estado' => 'vigente',
                        'msg' => "El carnet se encuentra vigente hasta la fecha: $venc",
                    ];
                }
            }
        }

        return [
            'estado' => null,
            'msg' => null,
        ];;
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

    public function getCodigoQr($idSolicitud)
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

    private function esDeshabilitado($data)
    {
        return $data["fecha_deshabilitado"] > date('Y-m-d');
    }

    public function getPodadoresPdf()
    {
        $params = ['estado' => 'aprobado', 'TOP' => 10000];
        $podadores = $this->getNoDeshabilitados($params, ['order' => ' ORDER BY id DESC ']);
        $header = array('Nro', 'DNI', 'NOMBRE', 'TELEFONO', 'INFO');

        $data = [];
        foreach ($podadores as $p) {
            $data[] = [
                $p['id'],
                $p['wapPersona']['Documento'],
                $p['wapPersona']['Nombre'],
                $p['wapPersona']['Celular'],
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

        // set font
        $pdf->SetFont('helvetica', '', 11);

        // add a page
        $pdf->AddPage();

        // print colored table
        $pdf->ColoredTable($header, utf8ize($data));

        // close and output PDF document
        $pdf->Output('Listado_podadores.pdf', 'D');
    }
}
