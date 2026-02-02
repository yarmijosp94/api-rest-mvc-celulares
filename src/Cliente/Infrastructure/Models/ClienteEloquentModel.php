<?php

namespace Src\Cliente\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Factura\Infrastructure\Models\FacturaEloquentModel;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;

class ClienteEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'clientes';

    protected $fillable = [
        'id',
        'tipo_documento',
        'numero_documento',
        'razon_social',
        'direccion',
        'telefono',
        'email'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(FacturaEloquentModel::class, 'cliente_id', 'id');
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(EquipoEloquentModel::class, 'cliente_id', 'id');
    }

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(\Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel::class, 'cliente_id', 'id');
    }
}
