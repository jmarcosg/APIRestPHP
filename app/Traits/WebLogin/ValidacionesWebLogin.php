<?php

namespace App\Traits\WebLogin;

trait ValidacionesWebLogin
{
    public static function checkParams($scope)
    {
        $errors = [];
        $scope = "validate$scope";
        $errors = self::$scope();
        if (count($errors) != 0) sendRes(null, 'Problema con los parametros', $errors);
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
}
