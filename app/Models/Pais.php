<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Pais extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

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
