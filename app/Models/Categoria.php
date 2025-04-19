<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Categoria extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'imagen',
        'estado',
    ];

    public function costos_envios()
    {
        return $this->belongsToMany(CostosEnvio::class, 'categorias_has_costos_envios', 'categorias_id', 'costos_envio_id');
    }

    public function cultivos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Cultivos::class,
            CultivosPredefinidos::class,
            'categoria_id', // Clave foránea en cultivos_predefinidos
            'cultivo_predefinido_id', // Clave foránea en cultivos
            'id', // Clave local en categorias
            'id' // Clave local en cultivos_predefinidos
        );
    }

	public function cultivos_predefinido(): HasMany
	{
		return $this->hasMany(CultivosPredefinidos::class, 'categoria_id', 'id');
	}
}
