<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'config_t_usuarios';
    protected $primaryKey = 'codigo_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'dni',
        'correo_electronico',
        'telefono',
        'codigo_rol',
        'codigo_institucion',
        'super_user',
        'usuario_drp',
        'estado'
    ];
}
