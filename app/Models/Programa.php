<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $table = 't_programas';
    protected $primaryKey = 'codigo_programa';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'nombre_programa', 
        'descripcion_programa', 
        'objetivo_programa', 
        'codigo_oficial_programa', 
        'codigo_institucion', 
        'estado_programa'
    ];
}
