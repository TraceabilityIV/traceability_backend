<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Direcciones extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = "direcciones";

    protected $fillable = [
        'direccion',
        'receptor',
        'latitud',
        'longitud',
        'barrio_id',
        'usuario_id',
        'estado'
    ];

    public function barrio()
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }
}
