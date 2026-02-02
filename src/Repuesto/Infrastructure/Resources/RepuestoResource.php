<?php

namespace Src\Repuesto\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepuestoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'marcaCompatible' => $this->marca_compatible,
            'modeloCompatible' => $this->modelo_compatible,
            'stockActual' => $this->stock_actual,
            'stockMinimo' => $this->stock_minimo,
            'precioCompra' => $this->precio_compra,
            'precioVenta' => $this->precio_venta,
            'proveedor' => $this->proveedor,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
