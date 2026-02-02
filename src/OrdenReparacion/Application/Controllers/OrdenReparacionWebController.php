<?php

namespace Src\OrdenReparacion\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Infrastructure\Mappers\OrdenReparacionMapper;
use Src\OrdenReparacion\Infrastructure\Requests\StoreOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\UpdateOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\AsignarRepuestoRequest;
use Src\OrdenReparacion\Infrastructure\Requests\ActualizarDiagnosticoRequest;
use Src\OrdenReparacion\Infrastructure\Requests\IniciarReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\EntregarEquipoRequest;
use Src\OrdenReparacion\Application\Services\OrdenReparacionService;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Mappers\ClienteMapper;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Mappers\EquipoMapper;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Mappers\TecnicoMapper;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Src\Repuesto\Infrastructure\Mappers\RepuestoMapper;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class OrdenReparacionWebController extends Controller
{
    protected OrdenReparacionService $ordenService;

    public function __construct(OrdenReparacionService $ordenService)
    {
        $this->ordenService = $ordenService;
    }

    public function index(): Response
    {
        $ordenes = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'repuestos'])->get();

        $ordenesData = $ordenes->map(
            fn($model) => OrdenReparacionMapper::toDomain($model)->toArray()
        )->toArray();

        return Inertia::render('OrdenReparacion/index', [
            'ordenes' => [
                'data' => $ordenesData,
                'links' => [],
                'meta' => [
                    'total' => count($ordenesData),
                    'per_page' => count($ordenesData),
                    'current_page' => 1,
                ]
            ],
            'stats' => [
                'total' => count($ordenesData),
                'pendientes' => $ordenes->whereIn('estado', ['recibido', 'en_diagnostico', 'diagnosticado', 'pendiente_aprobacion'])->count(),
                'en_proceso' => $ordenes->whereIn('estado', ['aprobado', 'en_reparacion', 'reparado'])->count(),
                'completadas' => $ordenes->where('estado', 'entregado')->count(),
                'canceladas' => $ordenes->where('estado', 'cancelado')->count(),
                'requieren_aprobacion' => $ordenes->where('requiere_aprobacion', true)
                    ->whereIn('estado', ['diagnosticado', 'pendiente_aprobacion'])->count(),
            ],
            'estados' => [
                'recibido' => 'Recibido',
                'en_diagnostico' => 'En Diagnóstico',
                'diagnosticado' => 'Diagnosticado',
                'pendiente_aprobacion' => 'Pendiente Aprobación',
                'aprobado' => 'Aprobado',
                'rechazado' => 'Rechazado',
                'en_reparacion' => 'En Reparación',
                'reparado' => 'Reparado',
                'entregado' => 'Entregado',
                'cancelado' => 'Cancelado',
            ],
        ]);
    }

    public function create(): Response
    {
        $clientes = ClienteEloquentModel::all();
        $tecnicos = TecnicoEloquentModel::where('activo', true)->with('user')->get();

        $clientesData = $clientes->map(
            fn($model) => ClienteMapper::toDomain($model)->toArray()
        )->toArray();

        $tecnicosData = $tecnicos->map(function ($model) {
            $tecnico = TecnicoMapper::toDomain($model)->toArray();
            $tecnico['name'] = $model->user->name ?? 'Desconocido';
            $tecnico['email'] = $model->user->email ?? '';
            return $tecnico;
        })->toArray();

        return Inertia::render('OrdenReparacion/create', [
            'clientes' => $clientesData,
            'equipos' => [],
            'repuestos' => [],
            'tecnicos' => $tecnicosData,
        ]);
    }

    public function getEquiposPorCliente(string $clienteId): JsonResponse
    {
        try {
            $equipos = EquipoEloquentModel::where('cliente_id', $clienteId)->get();

            $equiposData = $equipos->map(
                fn($model) => EquipoMapper::toDomain($model)->toArray()
            )->toArray();

            return response()->json([
                'equipos' => $equiposData,
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener equipos del cliente: ' . $e->getMessage());

            return response()->json([
                'error' => 'Error al obtener equipos del cliente',
                'equipos' => [],
            ], 500);
        }
    }

    public function store(StoreOrdenReparacionRequest $request): RedirectResponse
    {
        try {
            $orden = $this->ordenService->crearOrden($request->validated());

            return redirect()
                ->route('ordenes.index')
                ->with('success', "Orden de reparación {$orden->getNumeroOrden()} creada exitosamente");
        } catch (Exception $e) {
            Log::error('Error al crear orden de reparación: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'repuestos'])->findOrFail($id);
        $clientes = ClienteEloquentModel::all();
        $equipos = EquipoEloquentModel::all();
        $tecnicos = TecnicoEloquentModel::where('activo', true)->with('user')->get();
        $repuestos = RepuestoEloquentModel::all();

        $clientesData = $clientes->map(
            fn($model) => ClienteMapper::toDomain($model)->toArray()
        )->toArray();

        $equiposData = $equipos->map(
            fn($model) => EquipoMapper::toDomain($model)->toArray()
        )->toArray();

        $tecnicosData = $tecnicos->map(
            fn($model) => TecnicoMapper::toDomain($model)->toArray()
        )->toArray();

        $repuestosData = $repuestos->map(
            fn($model) => RepuestoMapper::toDomain($model)->toArray()
        )->toArray();

        return Inertia::render('OrdenReparacion/edit', [
            'orden' => OrdenReparacionMapper::toDomain($orden)->toArray(),
            'clientes' => $clientesData,
            'equipos' => $equiposData,
            'tecnicos' => $tecnicosData,
            'repuestos' => $repuestosData,
        ]);
    }

    public function update(UpdateOrdenReparacionRequest $request, string $id): RedirectResponse
    {
        try {
            $orden = $this->ordenService->actualizarOrden($id, $request->validated());

            return redirect()
                ->route('ordenes.index')
                ->with('success', "Orden de reparación {$orden->getNumeroOrden()} actualizada exitosamente");
        } catch (Exception $e) {
            Log::error('Error al actualizar orden de reparación: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'repuestos'])->findOrFail($id);

        return Inertia::render('OrdenReparacion/show', [
            'orden' => OrdenReparacionMapper::toDomain($orden)->toArray(),
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return redirect()
                ->back()
                ->with('error', 'Orden no encontrada');
        }

        try {
            $this->ordenService->cancelarOrden($id);

            return redirect()
                ->route('ordenes.index')
                ->with('success', 'Orden cancelada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al cancelar la orden: ' . $e->getMessage());
        }
    }

    public function aprobar(string $id): RedirectResponse
    {
        try {
            $orden = $this->ordenService->aprobarOrden($id);

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', "Orden {$orden->getNumeroOrden()} aprobada exitosamente");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al aprobar la orden: ' . $e->getMessage());
        }
    }

    public function rechazar(string $id, string $motivo): RedirectResponse
    {
        try {
            $orden = $this->ordenService->rechazarOrden($id, $motivo);

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', "Orden {$orden->getNumeroOrden()} rechazada");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al rechazar la orden: ' . $e->getMessage());
        }
    }

    public function asignarRepuesto(AsignarRepuestoRequest $request, string $id): RedirectResponse
    {
        try {
            $this->ordenService->asignarRepuesto(
                $id,
                $request->validated()['repuesto_id'],
                $request->validated()['cantidad']
            );

            return redirect()
                ->back()
                ->with('success', 'Repuesto asignado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al asignar repuesto: ' . $e->getMessage());
        }
    }

    public function removerRepuesto(string $ordenId, string $repuestoId): RedirectResponse
    {
        try {
            $this->ordenService->removerRepuesto($ordenId, $repuestoId);

            return redirect()
                ->back()
                ->with('success', 'Repuesto removido exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al remover repuesto: ' . $e->getMessage());
        }
    }

    public function actualizarDiagnostico(ActualizarDiagnosticoRequest $request, string $id): RedirectResponse
    {
        try {
            $this->ordenService->actualizarDiagnostico(
                $id,
                $request->validated()['diagnostico']
            );

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', 'Diagnóstico actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar diagnóstico: ' . $e->getMessage());
        }
    }

    public function iniciarReparacion(IniciarReparacionRequest $request, string $id): RedirectResponse
    {
        try {
            $this->ordenService->iniciarReparacion(
                $id,
                $request->validated()['tiempo_estimado'] ?? null
            );

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', 'Reparación iniciada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al iniciar reparación: ' . $e->getMessage());
        }
    }

    public function completarReparacion(string $id): RedirectResponse
    {
        try {
            $this->ordenService->completarReparacion($id);

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', 'Reparación completada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al completar reparación: ' . $e->getMessage());
        }
    }

    public function entregarEquipo(EntregarEquipoRequest $request, string $id): RedirectResponse
    {
        try {
            $this->ordenService->entregarEquipo(
                $id,
                $request->validated()['observaciones_entrega'] ?? null
            );

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', 'Equipo entregado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al entregar equipo: ' . $e->getMessage());
        }
    }

    public function iniciarDiagnostico(string $id): RedirectResponse
    {
        try {
            $this->ordenService->iniciarDiagnostico($id);

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', 'Diagnóstico iniciado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al iniciar diagnóstico: ' . $e->getMessage());
        }
    }

    public function completarDiagnostico(ActualizarDiagnosticoRequest $request, string $id): RedirectResponse
    {
        try {
            $this->ordenService->completarDiagnostico(
                $id,
                $request->validated()['diagnostico'],
                $request->validated()['costo_mano_obra'] ?? null,
                $request->validated()['requiere_aprobacion'] ?? false
            );

            return redirect()
                ->route('ordenes.show', $id)
                ->with('success', 'Diagnóstico completado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al completar diagnóstico: ' . $e->getMessage());
        }
    }
}
