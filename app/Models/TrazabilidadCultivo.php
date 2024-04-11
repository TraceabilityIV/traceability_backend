<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class TrazabilidadCultivo extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'trazabilidad_cultivos';

    protected $fillable = [
        'aplicacion',
        'descripcion',
        'resultados',
        'cultivo_id',
        'usuario_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cultivo()
    {
        return $this->belongsTo(Cultivos::class, 'cultivo_id');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'trazabilidad_cultivos_id');
    }
}
