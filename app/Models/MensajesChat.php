<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class MensajesChat extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = 'mensajes_chat';

    protected $fillable = [
        'mensaje',
        'usuario_envia_id',
        'usuario_recibe_id',
        'tipo',
    ];
}
