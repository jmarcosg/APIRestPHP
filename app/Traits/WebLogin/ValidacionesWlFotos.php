<?php

namespace App\Traits\WebLogin;

trait ValidacionesWlFotos
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

            case 'getLastFotos':
                $errors = self::validateGetLastFotos();
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
                $errors[] = 'id debe ser numérico';
            }
        }

        return $errors;
    }

    private static function validateSaveFoto()
    {
        $fillable = [
            'id',
            'id_app',
            'id_persona',
            'id_usuario',
            'nombre_archivo'
        ];

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
                $errors[] = 'id_app debe ser numérico';
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
                        $errors[] = 'id_persona debe ser numérico';
                    }
                } else {
                    if (!is_numeric($_POST['id_usuario'])) {
                        $errors[] = 'id_usuario debe ser numérico';
                    }
                }
            }
        }

        /* nombre_archivo */
        if (!isset($_POST['nombre_archivo'])) {
            $errors[] = 'nombre_archivo es requerido';
        }

        self::format($fillable);

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
                $errors[] = 'id debe ser numérico';
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

        self::format(['id', 'nombre_archivo']);

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
                $errors[] = 'id debe ser numérico';
            }
        }

        /* estado */
        if (!isset($_POST['estado'])) {
            $errors[] = 'estado es requerido';
        } else {
            if (!is_numeric($_POST['estado'])) {
                $errors[] = 'estado debe ser numérico';
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

        /* id_usuario_admin */
        if (!isset($_POST['id_usuario_admin'])) {
            $errors[] = 'id_usuario_admin es requerid_usuario_admino';
        } else {
            if (!is_numeric($_POST['id_usuario_admin'])) {
                $errors[] = 'id_usuario_admin debe ser numérico';
            }
        }

        $fillable = [
            'id',
            'estado',
            'observacion',
            'id_usuario_admin'
        ];

        self::format($fillable);

        return $errors;
    }

    private static function validateGetLastFotos()
    {
        $errors = [];

        /* dni */
        if (!isset($_POST['dni'])) {
            $errors[] = 'dni es requerido';
        } else {
            if (!is_numeric($_POST['dni'])) {
                $errors[] = 'dni debe ser numérico';
            }
        }

        return $errors;
    }

    private static function format($fillable)
    {
        foreach ($_POST as $key => $p) {
            if (!in_array($key, $fillable)) {
                unset($_POST[$key]);
            }
        }
    }
}
