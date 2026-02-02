<?php

namespace Src\OrdenReparacion\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\OrdenReparacion\Infrastructure\Requests\StoreOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\UpdateOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\CambiarEstadoRequest;
use Src\OrdenReparacion\Infrastructure\Requests\AsignarRepuestoRequest;
use Src\OrdenReparacion\Infrastructure\Resources\OrdenReparacionResource;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Application\Services\OrdenReparacionService;
use Illuminate\Http\JsonResponse;

class OrdenReparacionController extends Controller
{
    protected OrdenReparacionService $service;

    public function __construct(OrdenReparacionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $ordenes = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'repuestos'])->get();
        return OrdenReparacionResource::collection($ordenes);
    }

    public function store(StoreOrdenReparacionRequest $request)
    {
        $orden = OrdenReparacionEloquentModel::create($request->validated());
        $orden->load(['cliente', 'equipo', 'tecnico']);
        return new OrdenReparacionResource($orden);
    }

    public function show(string $id)
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'repuestos'])->find($id);

        if (!$orden) {
            return response()->json([
                'success' => false,
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        return new OrdenReparacionResource($orden);
    }

    public function update(UpdateOrdenReparacionRequest $request, string $id)
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'success' => false,
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        $orden->update($request->validated());
        $orden->load(['cliente', 'equipo', 'tecnico', 'repuestos']);
        
        return new OrdenReparacionResource($orden);
    }

    public function destroy(string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'success' => false,
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        if (!in_array($orden->estado, ['recibido', 'cancelado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden eliminar órdenes en estado recibido o cancelado'
            ], 400);
        }

        $orden->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Orden de reparación eliminada correctamente'
        ]);
    }

    public function cambiarEstado(CambiarEstadoRequest $request, string $id): JsonResponse
    {
        try {
            $orden = OrdenReparacionEloquentModel::findOrFail($id);
            
            if ($request->diagnostico_tecnico) {
                $orden->diagnostico_tecnico = $request->diagnostico_tecnico;
            }
            
            if ($request->solucion_aplicada) {
                $orden->solucion_aplicada = $request->solucion_aplicada;
            }
            
            if ($request->motivo_rechazo) {
                $orden->motivo_rechazo = $request->motivo_rechazo;
            }
            
            $orden->save();
            
            $this->service->cambiarEstado($orden, $request->nuevo_estado);
            $orden->load(['cliente', 'equipo', 'tecnico', 'repuestos']);
            
            return response()->json([
                'success' => true,
                'message' => 'Estado cambiado correctamente',
                'data' => new OrdenReparacionResource($orden)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function asignarRepuesto(AsignarRepuestoRequest $request, string $id): JsonResponse
    {
        try {
            $orden = OrdenReparacionEloquentModel::findOrFail($id);
            
            $this->service->asignarRepuesto($orden, $request->repuesto_id, $request->cantidad);
            $orden->load(['cliente', 'equipo', 'tecnico', 'repuestos']);
            
            return response()->json([
                'success' => true,
                'message' => 'Repuesto asignado correctamente',
                'data' => new OrdenReparacionResource($orden)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function quitarRepuesto(string $ordenId, string $repuestoId): JsonResponse
    {
        try {
            $orden = OrdenReparacionEloquentModel::findOrFail($ordenId);
            
            $this->service->quitarRepuesto($orden, $repuestoId);
            $orden->load(['cliente', 'equipo', 'tecnico', 'repuestos']);
            
            return response()->json([
                'success' => true,
                'message' => 'Repuesto removido correctamente',
                'data' => new OrdenReparacionResource($orden)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
