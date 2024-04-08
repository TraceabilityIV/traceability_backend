<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Pqr extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'pqrs';

    protected $fillable = [
        'nombres',
        'correo',
        'telefono',
        'direccion',
        'asunto',
        'descripcion',
        'usuario_id',
        'barrio_id',
    ];

    public function barrio()
    {
        return $this->belongsTo(Barrio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
