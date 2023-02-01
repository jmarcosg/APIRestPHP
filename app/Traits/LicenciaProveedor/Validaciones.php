<?php

namespace App\Traits\LicenciaProveedor;

trait Validaciones
{
    public static function checkParams($scope)
    {
        $errors = [];
        $scope = "validate$scope";
        $errors = self::$scope();
        cleanPost();
        if (count($errors) != 0) sendRes(null, 'Problema con los parametros', $errors);
    }

    private static function validatesaveTipoPersona()
    {
        $errors = [];

        if (!isset($_POST['id_usuario'])) {
            $errors[] = 'id_usuario es requerido';
        } else {
            if (!is_numeric($_POST['id_usuario'])) {
                $errors[] = 'id_usuario debe ser numérico';
            }
        }

        if (!isset($_POST['id_wappersonas'])) {
            $errors[] = 'id_wappersonas es requerido';
        } else {
            if (!is_numeric($_POST['id_wappersonas'])) {
                $errors[] = 'id_wappersonas debe ser numérico';
            }
        }

        if (!isset($_POST['tipo_persona'])) {
            $errors[] = 'tipo_persona es requerido - Puede ser fisica | juridica';
        } else {
            if ($_POST['tipo_persona'] != 'fisica' && $_POST['tipo_persona'] != 'juridica') {
                $errors[] = 'tipo_persona de ser fisica | juridica';
            } else {
                if ($_POST['tipo_persona'] == 'fisica') {
                    self::unsetPost(['cuit']);
                }

                if ($_POST['tipo_persona'] == 'juridica') {
                    if (!isset($_POST['cuit'])) {
                        $errors[] = 'cuit es requerido';
                    } else {
                        if (!is_numeric($_POST['cuit'])) {
                            $errors[] = 'cuit debe ser numérico';
                        }
                    }
                }
            }
        }

        return $errors;
    }

    private static function validatesaveDatosComerciales()
    {
        $errors = [];

        if (!isset($_POST['id_usuario'])) {
            $errors[] = 'id_usuario es requerido';
        } else {
            if (!is_numeric($_POST['id_usuario'])) {
                $errors[] = 'id_usuario debe ser numérico';
            }
        }

        if (!isset($_POST['id_wappersonas'])) {
            $errors[] = 'id_wappersonas es requerido';
        } else {
            if (!is_numeric($_POST['id_wappersonas'])) {
                $errors[] = 'id_wappersonas debe ser numérico';
            }
        }

        if (!isset($_POST["id_solicitud"])) {
            $errors[] = 'id_solicitud es requerido';
        } else {
            if (!is_numeric($_POST["id_solicitud"])) {
                $errors[] = 'id_solicitud debe ser numérico';
            }
        }

        if (!isset($_POST["razon_social"])) {
            $errors[] = 'razon_social es requerido';
        }

        if (!isset($_POST["telefono"])) {
            $errors[] = 'telefono es requerido';
        }

        if (!isset($_POST["direccion_comercial"])) {
            $errors[] = 'direccion_comercial es requerido';
        }

        if (!isset($_POST["cp_comercial"])) {
            $errors[] = 'cp_comercial es requerido';
        } else {
            if (!is_numeric($_POST["cp_comercial"])) {
                $errors[] = 'cp_comercial debe ser numérico';
            }
        }

        if (!isset($_POST["actividad_rubro"])) {
            $errors[] = 'actividad_rubro es requerido';
        }

        if (!isset($_POST["suscribe"])) {
            $errors[] = 'suscribe es requerido';
        }

        if (!isset($_POST["dni"])) {
            $errors[] = 'dni es requerido';
        }

        if (!isset($_POST["caracter"])) {
            $errors[] = 'caracter es requerido';
        }

        return $errors;
    }

    private static function validateagregarMiembro()
    {
        $errors = [];

        if (!isset($_POST["id_solicitud"])) {
            $errors[] = 'id_solicitud es requerido';
        } else {
            if (!is_numeric($_POST["id_solicitud"])) {
                $errors[] = 'id_solicitud debe ser numérico';
            }
        }

        if (!isset($_POST["nombre"])) {
            $errors[] = 'nombre es requerido';
        }

        return $errors;
    }

    private static function unsetPost($array)
    {
        foreach ($array as $value) {
            if (isset($_POST[$value])) unset($_POST[$value]);
        }
    }
}
