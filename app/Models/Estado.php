<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Estado extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'estados';

    protected $fillable = [
        'nombre',
        'icono_cumplido',
        'icono',
        'estado',
        'flag_inicial',
        'flag_final',
        'estado_siguiente_id',
    ];
}
