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
	];

	public function categoria(): BelongsTo
	{
		return $this->belongsTo(Categoria::class);
	}
}
