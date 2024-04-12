<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Precio extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'precios';

    protected $fillable = [
        'estado',
        'precio_venta',
        'cultivo_id',
        'tipo_id'
    ];

    public function cultivo()
    {
        return $this->belongsTo(Cultivos::class);
    }

    public function tipo()
    {
        return $this->belongsTo(Subagrupadores::class);
    }
}
