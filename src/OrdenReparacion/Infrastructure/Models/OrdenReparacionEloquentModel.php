<?php

namespace Src\OrdenReparacion\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Src\Factura\Infrastructure\Models\FacturaEloquentModel;

class OrdenReparacionEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'ordenes_reparacion';

    protected $fillable = [
        'id',
        'numero_orden',
        'cliente_id',
        'equipo_id',
        'tecnico_id',
        'fecha_ingreso',
        'fecha_promesa',
        'fecha_entrega',
        'falla_reportada',
        'diagnostico_tecnico',
        'solucion_aplicada',
        'estado',
        'prioridad',
        'costo_mano_obra',
        'costo_repuestos',
        'costo_total',
        'adelanto',
        'observaciones',
        'requiere_aprobacion',
        'aprobado_por_cliente',
        'fecha_aprobacion',
        'motivo_rechazo'
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_promesa' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'costo_mano_obra' => 'decimal:2',
        'costo_repuestos' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'adelanto' => 'decimal:2',
        'requiere_aprobacion' => 'boolean',
        'aprobado_por_cliente' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($orden) {
            if (empty($orden->numero_orden)) {
                $orden->numero_orden = self::generarNumeroOrden();
            }
        });
    }

    private static function generarNumeroOrden(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $num = $last ? ((int) substr($last->numero_orden, -4)) + 1 : 1;
        return 'ORD-' . $year . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteEloquentModel::class, 'cliente_id');
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoEloquentModel::class, 'equipo_id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'tecnico_id');
    }

    public function repuestos(): BelongsToMany
    {
        return $this->belongsToMany(
            RepuestoEloquentModel::class,
            'orden_repuesto',
            'orden_reparacion_id',
            'repuesto_id'
        )->withPivot('id', 'cantidad', 'precio_unitario', 'subtotal')
         ->withTimestamps()
         ->using(OrdenRepuestoEloquentModel::class);
    }

    public function factura(): HasOne
    {
        return $this->hasOne(FacturaEloquentModel::class, 'orden_reparacion_id');
    }
}
