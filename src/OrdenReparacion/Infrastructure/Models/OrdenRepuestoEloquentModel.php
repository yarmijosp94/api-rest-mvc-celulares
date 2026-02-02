<?php

namespace Src\OrdenReparacion\Infrastructure\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasUuid;

class OrdenRepuestoEloquentModel extends Pivot
{
    use HasUuid;

    protected $table = 'orden_repuesto';

    public $incrementing = false;

    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'orden_reparacion_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
    
    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
}
