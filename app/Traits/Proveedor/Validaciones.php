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

        if (!isset($_POST["nombre_fantasia"])) {
            $errors[] = 'nombre_fantasia es requerido';
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

        if (!isset($_POST["direccion_legal"])) {
            $errors[] = 'direccion_legal es requerido';
        }

        if (!isset($_POST["cp_legal"])) {
            $errors[] = 'cp_legal es requerido';
        } else {
            if (!is_numeric($_POST["cp_legal"])) {
                $errors[] = 'cp_legal debe ser numérico';
            }
        }

        if (!isset($_POST["direccion_local_venta"])) {
            $errors[] = 'direccion_local_venta es requerido';
        }

        if (!isset($_POST["actividad_rubro"])) {
            $errors[] = 'actividad_rubro es requerido';
        }

        if (!isset($_POST["nombres_firmas"])) {
            $errors[] = 'nombres_firmas es requerido';
        }

        if (!isset($_POST["nombres_miembros"])) {
            $errors[] = 'nombres_miembros es requerido';
        }

        return $errors;
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


    private static function unsetPost($array)
    {
        foreach ($array as $value) {
            if (isset($_POST[$value])) unset($_POST[$value]);
        }
    }
}
