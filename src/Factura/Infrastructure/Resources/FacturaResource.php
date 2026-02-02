<?php

namespace Src\Factura\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Factura\Infrastructure\Resources\DetalleFacturaResource;
use Src\Auth\Infrastructure\Resources\UserResource;

class FacturaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numeroFactura' => $this->numero_factura,
            'serie' => $this->serie,
            'clienteId' => $this->cliente_id,
            'usuarioId' => $this->usuario_id,
            'ordenReparacionId' => $this->orden_reparacion_id,
            'tipoFactura' => $this->tipo_factura,
            'fechaEmision' => $this->fecha_emision,
            'fechaVencimiento' => $this->fecha_vencimiento,
            'subtotal' => $this->subtotal,
            'igv' => $this->igv,
            'descuento' => $this->descuento,
            'total' => $this->total,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'cliente' => $this->whenLoaded('cliente', function() {
                return [
                    'id' => $this->cliente->id,
                    'razonSocial' => $this->cliente->razon_social,
                    'numeroDocumento' => $this->cliente->numero_documento,
                ];
            }),
            'usuario' => $this->whenLoaded('usuario', function() {
                return [
                    'id' => $this->usuario->id,
                    'name' => $this->usuario->name,
                    'email' => $this->usuario->email,
                ];
            }),
            'ordenReparacion' => $this->whenLoaded('ordenReparacion', function() {
                return [
                    'id' => $this->orden_reparacion->id,
                    'numeroOrden' => $this->orden_reparacion->numero_orden,
                    'estado' => $this->orden_reparacion->estado,
                    'costoTotal' => $this->orden_reparacion->costo_total,
                ];
            }),
            'detalles' => $this->whenLoaded('detalles', function() {
                return DetalleFacturaResource::collection($this->detalles);
            }),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
