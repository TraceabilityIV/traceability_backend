<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factores extends Model
{
    use HasFactory;

	protected $table = 'factores';

	protected $fillable = [
		'nombre',
		'descripcion',
		'peso',
		'fecha_inicio',
		'fecha_fin',
		'barrio_id',
		'ciudad_id',
		'departamento_id',
		'pais_id',
		'latitud',
		'longitud',
		'radio',
		'poligono',
	];

	public function barrio(): BelongsTo
	{
		return $this->belongsTo(Barrio::class);
	}

	public function ciudad(): BelongsTo
	{
		return $this->belongsTo(Ciudad::class);
	}

	public function departamento(): BelongsTo
	{
		return $this->belongsTo(Departamento::class);
	}

	public function pais(): BelongsTo
	{
		return $this->belongsTo(Pais::class);
	}
}
