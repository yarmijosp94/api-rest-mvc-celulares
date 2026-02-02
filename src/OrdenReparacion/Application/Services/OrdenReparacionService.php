<?php

namespace Src\OrdenReparacion\Application\Services;

use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Infrastructure\Mappers\OrdenReparacionMapper;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Exception;

class OrdenReparacionService
{
    private const TRANSICIONES_PERMITIDAS = [
        'recibido' => ['en_diagnostico', 'cancelado'],
        'en_diagnostico' => ['diagnosticado', 'cancelado'],
        'diagnosticado' => ['pendiente_aprobacion', 'en_reparacion', 'cancelado'],
        'pendiente_aprobacion' => ['aprobado', 'rechazado'],
        'aprobado' => ['en_reparacion'],
        'rechazado' => ['cancelado'],
        'en_reparacion' => ['reparado'],
        'reparado' => ['entregado'],
        'entregado' => [],
        'cancelado' => []
    ];
    
    public function cambiarEstado(OrdenReparacionEloquentModel $orden, string $nuevoEstado): bool
    {
        $estadoActual = $orden->estado;
        
        if (!$this->esTransicionValida($estadoActual, $nuevoEstado)) {
            throw new Exception("No se puede cambiar de estado '$estadoActual' a '$nuevoEstado'. Transición no permitida.");
        }
        
        $orden->estado = $nuevoEstado;
        
        if ($nuevoEstado === 'aprobado') {
            $orden->aprobado_por_cliente = true;
            $orden->fecha_aprobacion = now();
        }
        
        if ($nuevoEstado === 'entregado') {
            $orden->fecha_entrega = now();
        }
        
        $orden->save();
        return true;
    }
    
    public function calcularCostos(OrdenReparacionEloquentModel $orden): void
    {
        $orden->load('repuestos');
        
        $costoRepuestos = $orden->repuestos->sum('pivot.subtotal');
        
        $orden->costo_repuestos = $costoRepuestos;
        $orden->costo_total = $orden->costo_mano_obra + $costoRepuestos;
        $orden->save();
    }
    
    public function asignarRepuesto(OrdenReparacionEloquentModel $orden, string $repuestoId, int $cantidad): void
    {
        $repuesto = RepuestoEloquentModel::findOrFail($repuestoId);
        
        if ($repuesto->stock_actual < $cantidad) {
            throw new Exception("Stock insuficiente. Disponible: {$repuesto->stock_actual}, solicitado: {$cantidad}");
        }
        
        $existente = $orden->repuestos()->where('repuesto_id', $repuestoId)->first();
        if ($existente) {
            throw new Exception("Este repuesto ya está asignado a la orden. Use la función de actualizar cantidad.");
        }
        
        $precioUnitario = $repuesto->precio_venta;
        $subtotal = $precioUnitario * $cantidad;
        
        $orden->repuestos()->attach($repuestoId, [
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal
        ]);
        
        $repuesto->stock_actual -= $cantidad;
        if ($repuesto->stock_actual == 0) {
            $repuesto->estado = 'agotado';
        } elseif ($repuesto->stock_actual <= $repuesto->stock_minimo) {
            $repuesto->estado = 'por_pedir';
        }
        $repuesto->save();
        
        $this->calcularCostos($orden);
    }
    
    public function quitarRepuesto(OrdenReparacionEloquentModel $orden, string $repuestoId): void
    {
        $pivot = $orden->repuestos()->where('repuesto_id', $repuestoId)->first();
        if (!$pivot) {
            throw new Exception("El repuesto no está asignado a esta orden.");
        }
        
        $cantidad = $pivot->pivot->cantidad;
        $orden->repuestos()->detach($repuestoId);
        
        $repuesto = RepuestoEloquentModel::find($repuestoId);
        if ($repuesto) {
            $repuesto->stock_actual += $cantidad;
            if ($repuesto->stock_actual > 0 && $repuesto->estado == 'agotado') {
                $repuesto->estado = 'disponible';
            } elseif ($repuesto->stock_actual > $repuesto->stock_minimo && $repuesto->estado == 'por_pedir') {
                $repuesto->estado = 'disponible';
            }
            $repuesto->save();
        }
        
        $this->calcularCostos($orden);
    }
    
    public function actualizarCantidadRepuesto(OrdenReparacionEloquentModel $orden, string $repuestoId, int $nuevaCantidad): void
    {
        $pivot = $orden->repuestos()->where('repuesto_id', $repuestoId)->first();
        if (!$pivot) {
            throw new Exception("El repuesto no está asignado a esta orden.");
        }
        
        $cantidadActual = $pivot->pivot->cantidad;
        $diferencia = $nuevaCantidad - $cantidadActual;
        
        $repuesto = RepuestoEloquentModel::findOrFail($repuestoId);
        
        if ($diferencia > 0 && $repuesto->stock_actual < $diferencia) {
            throw new Exception("Stock insuficiente para incrementar. Disponible: {$repuesto->stock_actual}");
        }
        
        $precioUnitario = $pivot->pivot->precio_unitario;
        $nuevoSubtotal = $precioUnitario * $nuevaCantidad;
        
        $orden->repuestos()->updateExistingPivot($repuestoId, [
            'cantidad' => $nuevaCantidad,
            'subtotal' => $nuevoSubtotal
        ]);
        
        $repuesto->stock_actual -= $diferencia;
        if ($repuesto->stock_actual == 0) {
            $repuesto->estado = 'agotado';
        } elseif ($repuesto->stock_actual <= $repuesto->stock_minimo) {
            $repuesto->estado = 'por_pedir';
        } elseif ($repuesto->stock_actual > $repuesto->stock_minimo) {
            $repuesto->estado = 'disponible';
        }
        $repuesto->save();
        
        $this->calcularCostos($orden);
    }
    
    public function aprobarOrden(OrdenReparacionEloquentModel $orden): bool
    {
        if ($orden->estado !== 'pendiente_aprobacion') {
            throw new Exception("Solo se pueden aprobar órdenes en estado 'pendiente_aprobacion'");
        }
        
        return $this->cambiarEstado($orden, 'aprobado');
    }
    
    public function rechazarOrden(OrdenReparacionEloquentModel $orden, string $motivo): bool
    {
        if ($orden->estado !== 'pendiente_aprobacion') {
            throw new Exception("Solo se pueden rechazar órdenes en estado 'pendiente_aprobacion'");
        }
        
        $orden->motivo_rechazo = $motivo;
        $orden->aprobado_por_cliente = false;
        $orden->save();
        
        return $this->cambiarEstado($orden, 'rechazado');
    }
    
    private function esTransicionValida(string $estadoActual, string $nuevoEstado): bool
    {
        return in_array($nuevoEstado, self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? []);
    }
    
    public function getTransicionesPermitidas(string $estadoActual): array
    {
        return self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? [];
    }
}
