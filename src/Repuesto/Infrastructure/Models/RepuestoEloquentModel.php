<?php

namespace Src\Repuesto\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RepuestoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'repuestos';

    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'marca_compatible',
        'modelo_compatible',
        'stock_actual',
        'stock_minimo',
        'precio_compra',
        'precio_venta',
        'proveedor',
        'ubicacion',
        'estado'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ordenesReparacion(): BelongsToMany
    {
        return $this->belongsToMany(
            \Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel::class,
            'orden_repuesto',
            'repuesto_id',
            'orden_reparacion_id'
        )->withPivot('id', 'cantidad', 'precio_unitario', 'subtotal')
         ->withTimestamps();
    }
}
