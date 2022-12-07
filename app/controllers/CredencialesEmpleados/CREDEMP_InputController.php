<?php

namespace App\Controllers\CredencialesEmpleados;

use App\Models\CredencialesEmpleados\CREDEMP_Input;

class CREDEMP_InputController
{
    public function __construct()
    {
        $GLOBALS['exect'][] = 'CREDEMP_input';
    }

    public static function index($param = [], $ops = [])
    {
        $data = new CREDEMP_Input();
        $data = $data->list($param, $ops)->value;
        return $data;
    }

    public static function store($res)
    {
        $data = new CREDEMP_Input();

        $res['max_length'] = $res['max_length'] == "" ? null : $res['max_length'];
        $res['min_length'] = $res['min_length'] == "" ? null : $res['min_length'];
        $res['max_number'] = $res['max_number'] == "" ? null : $res['max_number'];
        $res['min_number'] = $res['min_number'] == "" ? null : $res['min_number'];
        $res['regex'] = $res['regex'] == "" ? null : $res['regex'];
        $res['required'] = $res['required'] == "" ? 0 : 1;

        $data->set($res);
        return $data->save();
    }

    public static function update($res, $id)
    {
        $res['max_length'] = $res['max_length'] == "" ? null : $res['max_length'];
        $res['min_length'] = $res['min_length'] == "" ? null : $res['min_length'];
        $res['max_number'] = $res['max_number'] == "" ? null : $res['max_number'];
        $res['min_number'] = $res['min_number'] == "" ? null : $res['min_number'];
        $res['regex'] = $res['regex'] == "" ? null : $res['regex'];
        $res['required'] = $res['required'] == "" ? 0 : 1;

        $input = new CREDEMP_Input();
        return $input->update($res, $id);
    }

    public static function delete($id) {
        $input = new CREDEMP_Input();
        return $input->delete($id);
    }
}
