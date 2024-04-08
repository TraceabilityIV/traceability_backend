<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class AdjuntosPqr extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'adjuntos_pqrs';

    protected $fillable = [
        'nombre',
        'url',
        'tipo',
        'pqrs_id',
    ];

    public function pqrs()
    {
        return $this->belongsTo(Pqr::class);
    }
}
