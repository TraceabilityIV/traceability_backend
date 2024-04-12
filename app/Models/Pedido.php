<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Pedido extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'pedidos';
    protected $fillable = [
        'usuario_id',
        'direccion_id',
        'estado_pedido_id',
        'total',
        'subtotal',
        'saldo',
        'metodo_pago',
        'tipo_pago',
    ];

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function direccion(){
        return $this->belongsTo(Direcciones::class, 'direccion_id');
    }

    public function estado(){
        return $this->belongsTo(Estado::class, 'estado_pedido_id');
    }


}
