<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poa extends Model
{
    protected $table = 'poa_t_poas';
    protected $primaryKey = 'codigo_poa';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'nombre_poa', 
        'descripcion_poa', 
        'codigo_institucion', 
        'codigo_programa', 
        'codigo_usuario_crea',
        'fecha_poa',
        'estado_poa'
    ];
}
