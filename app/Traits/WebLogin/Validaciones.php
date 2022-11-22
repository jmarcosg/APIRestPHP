<?php

namespace App\Traits\WebLogin;

trait Validaciones
{
    public static function checkParams($scope)
    {

        $errors = [];

        switch ($scope) {
            case 'getFotoById':
                $errors = self::validateGetFotosById();
                break;

            case 'saveFoto':
                $errors = self::validateSaveFoto();
                break;

            case 'editFotoByUser':
                $errors = self::validateEditFotoByUser();
                break;

            case 'changeEstado':
                $errors = self::validateChangeEstado();
                break;
        }

        if (count($errors) != 0) sendRes(null, 'Problema con los parametros', $errors);
    }

    private static function validateGetFotosById()
    {
        $errors = [];

        /* id */
        if (!isset($_GET['id'])) {
            $errors[] = 'id es requerido';
        } else {
            if (!is_numeric($_GET['id'])) {
                $errors[] = 'id debe ser numerico';
            }
        }

        return $errors;
    }

    private static function validateSaveFoto()
    {
        $errors = [];

        /* foto_perfil */
        if (!isset($_FILES['foto_perfil'])) {
            $errors[] = 'foto_perfil es requerido';
        }

        /* foto_dni */
        if (!isset($_FILES['foto_dni'])) {
            $errors[] = 'foto_dni es requerido';
        }

        /* id_app */
        if (!isset($_POST['id_app'])) {
            $errors[] = 'id_app es requerido';
        } else {
            if (!is_numeric($_POST['id_app'])) {
                $errors[] = 'id_app debe ser numerico';
            }
        }

        /* id_persona | id_usuario*/
        if (!isset($_POST['id_persona']) && !isset($_POST['id_usuario'])) {
            $errors[] = 'id_persona o id_usuario es requerido';
        } else {
            if (isset($_POST['id_persona']) && isset($_POST['id_usuario'])) {
                $errors[] = 'id_persona o id_usuario no ambos';
            } else {
                if (isset($_POST['id_persona'])) {
                    if (!is_numeric($_POST['id_persona'])) {
                        $errors[] = 'id_persona debe ser numerico';
                    }
                } else {
                    if (!is_numeric($_POST['id_usuario'])) {
                        $errors[] = 'id_usuario debe ser numerico';
                    }
                }
            }
        }

        /* nombre_archivo */
        if (!isset($_POST['nombre_archivo'])) {
            $errors[] = 'nombre_archivo es requerido';
        }

        return $errors;
    }

    private static function validateEditFotoByUser()
    {
        $errors = [];

        /* id */
        if (!isset($_POST['id'])) {
            $errors[] = 'id es requerido';
        } else {
            if (!is_numeric($_POST['id'])) {
                $errors[] = 'id debe ser numerico';
            }
        }

        /* nombre_archivo */
        if (!isset($_POST['nombre_archivo'])) {
            $errors[] = 'nombre_archivo es requerido';
        }

        /* foto_perfil | foto_dni */
        if (!isset($_FILES['foto_perfil']) && !isset($_FILES['foto_dni'])) {
            $errors[] = 'foto_perfil o foto_dni es requerido';
        }

        return $errors;
    }

    private static function validateChangeEstado()
    {
        $errors = [];

        /* id */
        if (!isset($_POST['id'])) {
            $errors[] = 'id es requerido';
        } else {
            if (!is_numeric($_POST['id'])) {
                $errors[] = 'id debe ser numerico';
            }
        }

        /* estado */
        if (!isset($_POST['estado'])) {
            $errors[] = 'estado es requerido';
        } else {
            if (!is_numeric($_POST['estado'])) {
                $errors[] = 'estado debe ser numerico';
            } else {
                if ($_POST['estado'] != 1 && $_POST['estado'] != -1) {
                    $errors[] = 'estado invalido: 1 - aprobado | -1 - rechazada ';
                } else {
                    if ($_POST['estado'] == 1 && !isset($_FILES['img'])) {
                        $errors[] = 'img es requerido';
                    }
                    if ($_POST['estado'] == -1 && !isset($_POST['observacion'])) {
                        $errors[] = 'observacion es requerido';
                    }
                }
            }
        }

        return $errors;
    }
}
