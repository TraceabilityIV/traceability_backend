<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Menu extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'menus';

    protected $fillable = [
        'nombre',
        'id_referencia',
        'icono',
        'color',
        'tipo_icono',
        'estado',
        'permiso_id',
    ];

    public function permiso()
    {
        return $this->belongsTo(Permisos::class, 'permiso_id');
    }
}
