<?php

namespace Src\Equipo\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;

class EquipoEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'equipos';

    protected $fillable = [
        'cliente_id',
        'marca',
        'modelo',
        'imei',
        'numero_serie',
        'color',
        'patron_bloqueo',
        'observaciones_iniciales',
        'estado_fisico',
        'accesorios'
    ];

    protected $casts = [
        'accesorios' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteEloquentModel::class, 'cliente_id', 'id');
    }

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(\Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel::class, 'equipo_id', 'id');
    }
}
