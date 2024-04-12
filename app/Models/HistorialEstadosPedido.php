<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class HistorialEstadosPedido extends Model implements Auditable
{
    use HasFactory, AuditingAuditable;

    protected $table = 'historial_estados_pedidos';

    protected $fillable = [
        'pedido_id',
        'estado_id',
        'usuario_id',
        'estado_siguiente_id',
    ];
}
