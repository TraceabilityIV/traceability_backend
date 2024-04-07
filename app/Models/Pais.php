<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pais extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'bandera',
        'indicador',
        'codigo_postal',
        'estado',
    ];
}
