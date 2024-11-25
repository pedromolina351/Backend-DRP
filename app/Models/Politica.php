<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Politica extends Model
{
    protected $table = 't_politicas_publicas';
    protected $primaryKey = 'codigo_politica_publica';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'nombre_politica_publica', 
        'descripcion_politica_publica', 
        'estado_politica_publica'
    ];
}
