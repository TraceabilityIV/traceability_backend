<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class TrazabilidadTransporte extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'trazabilidad_transportes';

    protected $fillable = [
        'descripcion',
        'fecha',
        'observaciones',
        'flag_entregado',
        'usuario_id',
        'pedido_id',
    ];
}
