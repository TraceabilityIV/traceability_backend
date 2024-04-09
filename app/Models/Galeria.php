<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Galeria extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = "galerias";

    protected $fillable = [
        'nombre',
        'url',
        'tipo',
        'cultivo_id'
    ];

    public function cultivo(){
        return $this->belongsTo(Cultivos::class, 'cultivo_id', 'id');
    }

}
