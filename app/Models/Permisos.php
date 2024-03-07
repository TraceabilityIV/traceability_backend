<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as ContractsAuditable;

class Permisos extends Model implements ContractsAuditable
{
    use HasFactory, Auditable, SoftDeletes;

    protected $table = 'permisos';

    protected $fillable = [
        'name',
        'guard_name'
    ];
}
