<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Models\CredencialesEmpleados\CREDEMP_Template;

class CREDEMP_TemplateController
{
    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Template();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $template = self::index(['id' => $res['templateId']]);
        if (count($template) > 0) {
            $template = $template[0];
            $personas = CREDEMP_PersonaController::index(['id_template' => $template['id']]);
            if (count($personas) > 0) {
                $res['deshabilitado'] = 0;
                $data = new CREDEMP_Template();
                $data->set($res);
                $idTemplate = $data->save();

                $inputs = $res['inputs'];

                foreach ($inputs as $input) {
                    $input['name'] = $input['id'];
                    $input['id_template'] = $idTemplate;
                    $input['id_tipo'] = $input['tipo'];
                    unset($input['id']);
                    unset($input['tipo']);
                    CREDEMP_InputController::store($input);
                }
            } else {
                $inputs = $res['inputs'];
                unset($res['templateId']);
                unset($res['inputs']);

                self::update($res, $template['id']);

                $oldInputs = CREDEMP_InputController::index(['id_template' => $template['id']]);

                if (count($oldInputs) > count($inputs)) {
                    // print_r($oldInputs);
                    $i = count($oldInputs) - 1;
                    while ($i > count($inputs) - 1) {
                        $inputToDelete = $oldInputs[$i];
                        CREDEMP_InputController::delete($inputToDelete['id']);
                        $i--;
                    }
                }

                foreach ($inputs as $input) {
                    $input['name'] = $input['id'];
                    $input['id_tipo'] = $input['tipo'];
                    $input['id_template'] = $template['id'];
                    unset($input['id']);
                    unset($input['tipo']);

                    $oldInput = CREDEMP_InputController::index(['id_template' => $template['id'], 'name' => $input['name']]);
                    if (count($oldInput) > 0) {
                        $oldInput = $oldInput[0];
                        CREDEMP_InputController::update($input, $oldInput['id']);
                    } else {
                        CREDEMP_InputController::store($input);
                    }
                }
                $idTemplate = $template['id'];
            }
        } else {
            $res['deshabilitado'] = 0;
            $res['needed_inputs'] = $res['needed_inputs'] == "" ? 0 : $res['needed_inputs'];
            $data = new CREDEMP_Template();
            $data->set($res);
            $idTemplate = $data->save();

            if ($res['needed_inputs'] != 0) {

                $inputs = $res['inputs'];

                foreach ($inputs as $input) {
                    $input['name'] = $input['id'];
                    $input['id_template'] = $idTemplate;
                    $input['id_tipo'] = $input['tipo'];
                    unset($input['id']);
                    unset($input['tipo']);
                    CREDEMP_InputController::store($input);
                }
            }
        }

        return $idTemplate;
    }

    public static function update($res, $id)
    {
        $template = new CREDEMP_Template();
        return $template->update($res, $id);
    }
}
