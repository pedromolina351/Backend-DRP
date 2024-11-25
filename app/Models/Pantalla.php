<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pantalla extends Model
{
    protected $table = 'config_t_pantallas';
    protected $primaryKey = 'codigo_pantalla';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'codigo_modulo',
        'nombre_pantalla', 
        'url', 
        'estado'
    ];
}
