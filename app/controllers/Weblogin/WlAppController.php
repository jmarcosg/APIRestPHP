<?php

namespace App\Controllers\Weblogin;

use App\Models\BaseModel;
use ErrorException;

class WlAppController
{
    public static function getApps()
    {
        $apps = self::getSqlApps();

        if (!$apps instanceof ErrorException) {
            $apps = self::formatApps($apps);
            sendRes([
                'aplicaciones' => $apps['aplicaciones'],
                'categorias' => $apps['categorias']
            ]);
        }
        exit;
    }

    public static function formatApps($apps)
    {
        /* Retiramos los espacios en blanco */
        foreach ($apps as $key => $app) {
            $apps[$key]['nombre'] = trim($app['nombre']);
            $apps[$key]['class'] = 'show-tramite';
            $apps[$key]['title'] = $app['title'];
        }

        $aplicaciones = [
            'aplicaciones' => $apps,
            'categorias' => []
        ];

        $categorias = self::getSqlCategorias();
        foreach ($categorias as $key => $cat) {
            $caterogia = $cat['nombre'];
            $aplicaciones['categorias'][$caterogia] = array_filter($apps, function ($app) use ($caterogia) {
                return $app['categoria'] == $caterogia;
            });
            $aplicaciones['categorias'][$caterogia] = array_values($aplicaciones['categorias'][$caterogia]);
        }

        return $aplicaciones;
    }

    public static function getSqlApps()
    {
        $sql =
            "SELECT 
                REFERENCIA  as id,
                APLICACION as nombre,
                TITULO as title,
                URL as url,
                c.nombre as categoria
            FROM dbo.wlAplicaciones a
            LEFT JOIN wlAppsCategorias ac ON ac.id_app = a.REFERENCIA 
            LEFT JOIN wlCategorias c ON c.id = ac.id_categoria";

        $model = new BaseModel();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }

    public static function getSqlCategorias()
    {
        $sql = "SELECT nombre as nombre FROM dbo.wlCategorias";

        $model = new BaseModel();
        $result = $model->executeSqlQuery($sql, false);

        return $result;
    }
}
