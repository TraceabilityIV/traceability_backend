<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\RefreshesPermissionCache;

class Roles extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;
    use HasPermissions;
    use RefreshesPermissionCache;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name'
    ];

    public function permisos(){

        return $this->belongsToMany(Permisos::class, 'roles_has_permisos', 'permiso_id', 'role_id',);
    }
}
