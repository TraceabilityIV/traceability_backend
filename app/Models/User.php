<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as ContractsAuditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements ContractsAuditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Auditable, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'nombres',
        'apellidos',
        'telefono',
        'estado',
        'avatar',
        'doc_identificacion',
        'rut',
        'contrato',
        'documentacion_valida',
        'tipo_cliente',
        'paso_validacion_documentos'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function cultivos_favoritos(){
        return $this->belongsToMany(Cultivos::class, 'cultivos_favoritos', 'usuario_id', 'cultivo_id');
    }

    public function cultivos(){
        return $this->hasMany(Cultivos::class, 'usuario_id', 'id');
    }

	public function sendPasswordResetNotification($token)
	{
		$this->notify(new CustomResetPassword($token, $this->email));
	}
}
