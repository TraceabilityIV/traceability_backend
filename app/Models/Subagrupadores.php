<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Subagrupadores extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'subagrupadores';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado',
        'agrupador_id'
    ];

    public function agrupador()
    {
        return $this->belongsTo(Agrupador::class, 'agrupador_id');
    }
}
