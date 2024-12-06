<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjetivoVisionPais extends Model
{
    protected $table = 't_objetivos_vision_pais';
    protected $primaryKey = 'codigo_objetivo_vision_pais';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'objetivo_vision_pais',
        'estado_objetivo_vision_pais'
    ];
}
