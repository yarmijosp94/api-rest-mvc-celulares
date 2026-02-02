<?php

namespace Src\Equipo\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clienteId' => $this->cliente_id,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'imei' => $this->imei,
            'numeroSerie' => $this->numero_serie,
            'color' => $this->color,
            'patronBloqueo' => $this->patron_bloqueo,
            'observacionesIniciales' => $this->observaciones_iniciales,
            'estadoFisico' => $this->estado_fisico,
            'accesorios' => $this->accesorios,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
