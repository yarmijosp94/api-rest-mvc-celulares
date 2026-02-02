<?php

namespace Src\ConsultaCliente\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultaOrdenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numeroOrden' => $this->numero_orden,
            'cliente' => [
                'razonSocial' => $this->cliente->razon_social,
                'email' => $this->cliente->email,
                'telefono' => $this->cliente->telefono,
            ],
            'equipo' => [
                'marca' => $this->equipo->marca,
                'modelo' => $this->equipo->modelo,
                'imei' => $this->equipo->imei,
                'color' => $this->equipo->color,
            ],
            'estado' => $this->estado,
            'estadoLabel' => $this->getEstadoLabel(),
            'estadoColor' => $this->getEstadoColor(),
            'fechaIngreso' => $this->fecha_ingreso?->format('d/m/Y H:i'),
            'fechaPromesa' => $this->fecha_promesa?->format('d/m/Y'),
            'fechaEntrega' => $this->fecha_entrega?->format('d/m/Y'),
            'fallaReportada' => $this->falla_reportada,
            'diagnosticoTecnico' => $this->diagnostico_tecnico,
            'solucionAplicada' => $this->solucion_aplicada,
            'costoManoObra' => $this->costo_mano_obra,
            'costoRepuestos' => $this->costo_repuestos,
            'costoTotal' => $this->costo_total,
            'adelanto' => $this->adelanto,
            'repuestos' => $this->when($this->repuestos, function() {
                return $this->repuestos->map(fn($r) => [
                    'nombre' => $r->nombre,
                    'cantidad' => $r->pivot->cantidad,
                    'precioUnitario' => $r->pivot->precio_unitario,
                    'subtotal' => $r->pivot->subtotal,
                ]);
            }),
            'observaciones' => $this->observaciones,
            'tecnico' => $this->when($this->tecnico, fn() => [
                'nombre' => $this->tecnico->name
            ]),
            'requiereAprobacion' => $this->requiere_aprobacion,
            'aprobadoPorCliente' => $this->aprobado_por_cliente,
            'fechaAprobacion' => $this->fecha_aprobacion?->format('d/m/Y H:i'),
            'motivoRechazo' => $this->motivo_rechazo,
        ];
    }

    private function getEstadoLabel(): string
    {
        $labels = [
            'recibido' => 'Recibido',
            'en_diagnostico' => 'En Diagnóstico',
            'diagnosticado' => 'Diagnosticado',
            'pendiente_aprobacion' => 'Pendiente de Aprobación',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado',
            'en_reparacion' => 'En Reparación',
            'reparado' => 'Reparado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];
        return $labels[$this->estado] ?? $this->estado;
    }

    private function getEstadoColor(): string
    {
        $colors = [
            'recibido' => 'gray',
            'en_diagnostico' => 'blue',
            'diagnosticado' => 'cyan',
            'pendiente_aprobacion' => 'yellow',
            'aprobado' => 'green',
            'rechazado' => 'red',
            'en_reparacion' => 'orange',
            'reparado' => 'emerald',
            'entregado' => 'purple',
            'cancelado' => 'neutral',
        ];
        return $colors[$this->estado] ?? 'gray';
    }
}
