<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Comision extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'comisiones';

    protected $fillable = [
        'nombre',
        'porcentaje',
        'estado',
    ];

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'comisiones_has_categorias', 'comision_id', 'categorias_id');
    }

    public function tipo_precios()
    {
        return $this->belongsToMany(Subagrupadores::class, 'comisiones_has_tipo_precio_ventas', 'comision_id', 'tipo_precio_id');
    }
}
