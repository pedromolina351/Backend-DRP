<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'config_t_roles';
    protected $primaryKey = 'codigo_rol';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'nombre_rol', 
        'descripcion_rol', 
        'estado'
    ];
}
