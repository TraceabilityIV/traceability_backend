<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Barrio extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    protected $table = "barrios";

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'codigo_postal',
        'estado',
        'ciudad_id'
    ];
}
