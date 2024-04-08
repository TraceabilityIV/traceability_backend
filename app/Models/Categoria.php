<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Categoria extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'imagen',
        'estado',
    ];
}
