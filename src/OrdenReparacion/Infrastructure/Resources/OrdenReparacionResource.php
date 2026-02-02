<?php

namespace Src\OrdenReparacion\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Equipo\Infrastructure\Resources\EquipoResource;
use Src\Auth\Infrastructure\Resources\UserResource;
use Src\Repuesto\Infrastructure\Resources\RepuestoResource;

class OrdenReparacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numeroOrden' => $this->numero_orden,
            'clienteId' => $this->cliente_id,
            'equipoId' => $this->equipo_id,
            'tecnicoId' => $this->tecnico_id,
            'fechaIngreso' => $this->fecha_ingreso?->format('Y-m-d H:i:s'),
            'fechaPromesa' => $this->fecha_promesa?->format('Y-m-d H:i:s'),
            'fechaEntrega' => $this->fecha_entrega?->format('Y-m-d H:i:s'),
            'fallaReportada' => $this->falla_reportada,
            'diagnosticoTecnico' => $this->diagnostico_tecnico,
            'solucionAplicada' => $this->solucion_aplicada,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'costoManoObra' => (float) $this->costo_mano_obra,
            'costoRepuestos' => (float) $this->costo_repuestos,
            'costoTotal' => (float) $this->costo_total,
            'adelanto' => $this->adelanto ? (float) $this->adelanto : null,
            'observaciones' => $this->observaciones,
            'requiereAprobacion' => (bool) $this->requiere_aprobacion,
            'aprobadoPorCliente' => $this->aprobado_por_cliente !== null ? (bool) $this->aprobado_por_cliente : null,
            'fechaAprobacion' => $this->fecha_aprobacion?->format('Y-m-d H:i:s'),
            'motivoRechazo' => $this->motivo_rechazo,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            'cliente' => $this->whenLoaded('cliente', function () {
                return new ClienteResource($this->cliente);
            }),
            'equipo' => $this->whenLoaded('equipo', function () {
                return new EquipoResource($this->equipo);
            }),
            'tecnico' => $this->whenLoaded('tecnico', function () {
                return new UserResource($this->tecnico);
            }),
            'repuestos' => $this->whenLoaded('repuestos', function () {
                return $this->repuestos->map(function ($repuesto) {
                    $resource = new RepuestoResource($repuesto);
                    $data = $resource->toArray(request());
                    $data['pivot'] = [
                        'cantidad' => $repuesto->pivot->cantidad,
                        'precioUnitario' => (float) $repuesto->pivot->precio_unitario,
                        'subtotal' => (float) $repuesto->pivot->subtotal,
                    ];
                    return $data;
                });
            }),
        ];
    }
}
