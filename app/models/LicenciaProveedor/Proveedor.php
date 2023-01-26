<?php

namespace App\Models\LicenciaProveedor;

use App\Models\BaseModel;

class Proveedor extends BaseModel
{
    protected $table = 'pro_solicitudes';
    protected $logPath = 'v1/proveedores';
    protected $identity = 'id';

    protected $fillable = [
        'id_usuario',
        'id_wappersonas',

        'tipo_persona',
        'titular',
        'cuit',

        'id_wappersonas_tercero',
        'dni_tercero',
        'tramite_tercero',
        'genero_tercero',

        'razon_social',
        'telefono',
        'nombre_fantasia',
        'direccion_comercial',
        'cp_comercial',
        'direccion_legal',
        'cp_legal',

        'nombres_firmas',
        'nombres_miembros',

        'reg_comercio_num',
        'reg_comercio_libro',
        'reg_comercio_folio',
        'reg_comercio_anio',
        'es_socio',
        'nombres_socios',

        'hab_comercial_num',
        'cuit_num',
        'ingresos_brutos_num',
        'convenio_multi_num',
        'cbu',
        'reg_preveedores_num',
        'condicion_iva',

        'suscribe',
        'dni',
        'caracter',

        'estado',

        'observacion',
    ];
}
