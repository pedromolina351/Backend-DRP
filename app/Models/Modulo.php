<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'config_t_modulos';
    protected $primaryKey = 'codigo_modulo';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'nombre_modulo', 
        'estado'
    ];
}
