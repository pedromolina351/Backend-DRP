<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 't_metas_an_ods';
    protected $primaryKey = 'codigo_meta_an_ods';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'codigo_objetivo_an_ods',
        'meta_an_ods',
        'estado_meta_an_ods'
    ];
}
