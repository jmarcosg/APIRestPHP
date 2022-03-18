<?php

namespace App\Models;

/**
 * This is the model class for table "DeportesUsuario".
 *
 * @property int $id_wappersona
 * @property string $nombre
 * @property string $apellido
 * 
 */
class DeportesUsuario extends BaseModel
{
    protected $table = 'deportes_usuarios';
    protected $logPath = 'v1/deportes_usuarios';
    protected $identity = 'id';
    protected $softDeleted = 'deleted_at';

    protected $fillable = ['id_wappersonas', 'nombre', 'apellido'];
}
