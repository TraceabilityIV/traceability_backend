<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class CostosEnvio extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'costos_envios';

    protected $fillable = [
        'costo',
        'estado',
        'tipo_costo_id'
    ];

    public function tipo_costo()
    {
        return $this->belongsTo(Subagrupadores::class, 'tipo_costo_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categorias_has_costos_envios', 'costos_envio_id', 'categorias_id');
    }
}
