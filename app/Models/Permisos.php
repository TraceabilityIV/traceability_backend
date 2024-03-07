<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as ContractsAuditable;

class Permisos extends Model implements ContractsAuditable
{
    use HasFactory, Auditable;

    protected $table = 'permisos';

    protected $fillable = [
        'name',
        'guard_name'
    ];
}
