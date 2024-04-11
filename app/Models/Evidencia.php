<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Evidencia extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'evidencias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'url',
        'tipo',
        'trazabilidad_cultivos_id'
    ];

    public function trazabilidad_cultivos(){
        return $this->belongsTo(TrazabilidadCultivo::class, 'trazabilidad_cultivos_id');
    }
}
