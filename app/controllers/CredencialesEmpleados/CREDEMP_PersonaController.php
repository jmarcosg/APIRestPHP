<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Models\CredencialesEmpleados\CREDEMP_Codigo_QR;
use App\Models\CredencialesEmpleados\CREDEMP_Persona;
use APP\Models\CredencialesEmpleados\CREDEMP_Valor;
use ErrorException;

class CREDEMP_PersonaController
{
    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Persona();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $template = CREDEMP_TemplateController::index(['id' => $res['templateId']])[0];

        if ($template['deshabilitado'] == 0) {
            $personaBuscada = self::index(['dni' => $res['data']['estaticos']['dni'], 'id_template' => $res['templateId']]);
            // echo count($personaBuscada);
            // exit;
            if (count($personaBuscada) == 0) {
                $idQr = CREDEMP_CodigoQRController::store($res);

                if (!$idQr instanceof ErrorException) {
                    $idTemplate = $res['templateId'];
                    $res['data']['estaticos']['deshabilitado'] = 0;
                    $res['data']['estaticos']['id_template'] = $idTemplate;

                    $data = new CREDEMP_Persona();
                    $data->set($res['data']['estaticos']);
                    $idPersona = $data->save();

                    $i = 1;
                    $ok = true;

                    if ($template['needed_inputs'] != "0") {
                        while (!$ok instanceof ErrorException && $i <= count($res['data']['dinamicos'])) {
                            $input = $res['data']['dinamicos']["input$i"];

                            $inputObj = CREDEMP_InputController::index(['id_template' => $idTemplate, 'name' => "input$i"])[0];

                            $value = [
                                'valor' => $input,
                                'id_template' => $idTemplate,
                                'id_persona' => $idPersona,
                                'id_input' => $inputObj['id']
                            ];
                            $ok = CREDEMP_ValorController::store($value);
                            $i++;
                        }
                    }
                    if ($ok instanceof ErrorException) {
                        $idPersona = new ErrorException();
                    } else {
                        CREDEMP_CodigoQRController::update(['id_persona_identificada' => $idPersona], $idQr);
                    }
                } else {
                    $idPersona = new ErrorException();
                }
            } else {
                $persona = $personaBuscada[0];
                $res['data']['estaticos']['deshabilitado'] = 0;
                $idPersona = $persona['id'];
                if (CREDEMP_PersonaController::update($res['data']['estaticos'], $idPersona)) {
                    $dinamicos = $res['data']['dinamicos'];
                    foreach ($dinamicos as $key => $valor) {
                        $input = CREDEMP_InputController::index(['id_template' => $persona['id_template'], 'name' => $key])[0];
                        $objValor = CREDEMP_ValorController::index(['id_persona' => $idPersona, 'id_template' => $persona['id_template'], 'id_input' => $input['id']])[0];
                        CREDEMP_ValorController::update(['valor' => $valor], $objValor['id']);
                    }
                }
            }
        } else {
            $idPersona = new ErrorException();
        }

        return $idPersona;
    }

    public static function update($data, $id)
    {
        $persona = new CREDEMP_Persona();
        return $persona->update($data, $id);
    }
}
