<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CultivosPredefinidos extends Model
{
    use HasFactory;

	protected $table = 'cultivos_predefinidos';

	protected $fillable = [
		'nombre',
		'nombre_corto',
		'categoria_id',
		'imagen',
		'temperatura_min',
		'temperatura_max',
		'ph_min',
		'ph_max',
		'dias_crecimiento',
		'profundidad_suelo',
		'textura_suelo',
	];

	public function categoria(): BelongsTo
	{
		return $this->belongsTo(Categoria::class);
	}
}
