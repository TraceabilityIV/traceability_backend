<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Cultivos extends Model implements Auditable
{
    use HasFactory, AuditingAuditable, SoftDeletes;

    protected $table = "cultivos";

    protected $fillable = [
        'nombre',
        'estado',
        'ubicacion',
        'direccion',
        'latitud',
        'longitud',
        'fecha_siembra',
        'area',
        'variedad',
        'nombre_corto',
        'lote',
        'prefijo_registro',
        'fecha_cosecha',
        'cantidad_aproximada',
        'usuario_id',
        'pedido_id',
        'categoria_id',
        'precio_venta'
    ];

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function galeria(){
        return $this->hasMany(Galeria::class, 'cultivo_id', 'id');
    }

    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    public function imagen(){
        return $this->hasOne(Galeria::class, 'cultivo_id', 'id');
    }

    public function precio(){
        return $this->hasOne(Precio::class,'cultivo_id', 'id');
    }

    public function trazabilidad()
    {
        return $this->hasMany(TrazabilidadCultivo::class, 'cultivo_id');
    }
}
