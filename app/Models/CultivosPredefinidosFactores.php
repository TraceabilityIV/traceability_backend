<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CultivosPredefinidosFactores extends Model
{
    use HasFactory, SoftDeletes;

	protected $table = 'cultivos_predefinidos_factores';

	protected $fillable = [
		'cultivo_predefinido_id',
		'factor_id',
		'descripcion',
		'valor_apoyo',
	];

	public function cultivo_predefinido(): BelongsTo
	{
		return $this->belongsTo(CultivosPredefinidos::class);
	}

	public function factor(): BelongsTo
	{
		return $this->belongsTo(Factores::class);
	}
}
