<?php

namespace App\Traits\Proveedor;

trait Validaciones
{
    public static function checkParams($scope)
    {
        $errors = [];
        $scope = "validate$scope";
        $errors = self::$scope();
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

                    if (!isset($_POST['titular'])) {
                        $errors[] = 'titular es requerido';
                    } else {

                        if (!($_POST['titular'] == 'propia' || $_POST['titular'] == 'tercero')) {
                            $errors[] = 'titular de ser propia | tercero';
                        } else {
                            if ($_POST['titular'] == 'propia') {

                                /* Borramos lo que no se requiere */
                                self::unsetPost(['id_wappersonas_tercero', 'genero_tercero', 'dni_tercero', 'tramite_tercero']);
                            }

                            if ($_POST['titular'] == 'tercero') {

                                if (!isset($_POST['id_wappersonas_tercero'])) {
                                    $errors[] = 'id_wappersonas_tercero es requerido';
                                } else {
                                    if (!is_numeric($_POST['id_wappersonas_tercero'])) {
                                        $errors[] = 'id_wappersonas_tercero debe ser numérico';
                                    }
                                }

                                if (!isset($_POST['genero_tercero'])) {
                                    $errors[] = 'genero_tercero es requerido';
                                }

                                if (!isset($_POST['dni_tercero'])) {
                                    $errors[] = 'dni_tercero es requerido';
                                } else {
                                    if (!is_numeric($_POST['dni_tercero'])) {
                                        $errors[] = 'dni_tercero debe ser numérico';
                                    }
                                }

                                if (!isset($_POST['tramite_tercero'])) {
                                    $errors[] = 'tramite_tercero es requerido';
                                } else {
                                    if (!is_numeric($_POST['tramite_tercero'])) {
                                        $errors[] = 'tramite_tercero debe ser numérico';
                                    }
                                }
                            }
                        }
                    }
                }

                if ($_POST['tipo_persona'] == 'juridica') {
                    self::unsetPost(['titular', 'id_wappersonas_tercero', 'genero_tercero', 'dni_tercero', 'tramite_tercero']);

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

    private static function validategetAllData()
    {
        $errors = [];
        if (!isset($_POST['procedures_started'])) {
            $errors[] = 'procedures_started es requerido';
        }

        if (!isset($_POST['genero'])) {
            $errors[] = 'genero es requerido';
        }

        if (!isset($_POST['dni'])) {
            $errors[] = 'dni es requerido';
        } else {
            if (!is_numeric($_POST['dni'])) {
                $errors[] = 'dni debe ser numérico';
            }
        }

        if (!isset($_POST['id_usuario'])) {
            $errors[] = 'id_usuario es requerido';
        } else {
            if (!is_numeric($_POST['id_usuario'])) {
                $errors[] = 'id_usuario debe ser numérico';
            }
        }
        return $errors;
    }

    private static function validategetUserData()
    {
        $errors = [];
        if (!isset($_POST['user'])) {
            $errors[] = 'El usuario es requerido';
        }

        if (!isset($_POST['pass'])) {
            $errors[] = 'La contraseña es requerida';
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
