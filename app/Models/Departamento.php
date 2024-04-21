<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Departamento extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'estado',
        'bandera',
        'indicador',
        'codigo_postal',
        'pais_id'
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

}
