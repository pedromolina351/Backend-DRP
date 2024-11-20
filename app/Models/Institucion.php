<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 't_instituciones';
    protected $primaryKey = 'codigo_institucion';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'nombre_institucion', 
        'codigo_oficial_institucion', 
        'mision_institucion', 
        'vision_institucion', 
        'certificada',
        'estado_institucion'
    ];
}
