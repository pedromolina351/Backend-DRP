<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    protected $table = 't_objetivos_an_ods';
    protected $primaryKey = 'codigo_objetivo_an_ods';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'objetivo_an_ods', 
        'estado_objetivo_an_ods'
    ];
}
