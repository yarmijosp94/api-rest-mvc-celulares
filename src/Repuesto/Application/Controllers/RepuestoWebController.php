<?php

namespace Src\Repuesto\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Src\Repuesto\Infrastructure\Mappers\RepuestoMapper;
use Src\Repuesto\Infrastructure\Requests\StoreRepuestoRequest;
use Src\Repuesto\Infrastructure\Requests\UpdateRepuestoRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class RepuestoWebController extends Controller
{
    public function index(): Response
    {
        $repuestos = RepuestoEloquentModel::all();

        $repuestosData = $repuestos->map(
            fn($model) => RepuestoMapper::toDomain($model)->toArray()
        )->toArray();

        $totalRepuestos = count($repuestosData);
        $stockBajo = $repuestos->filter(
            fn($repuesto) => $repuesto->stock_actual < $repuesto->stock_minimo
        )->count();

        $valorTotalInventario = $repuestos->sum(function ($repuesto) {
            return $repuesto->stock_actual * $repuesto->precio_venta;
        });

        return Inertia::render('Repuesto/index', [
            'repuestos' => [
                'data' => $repuestosData,
                'links' => [],
                'meta' => [
                    'total' => $totalRepuestos,
                    'per_page' => $totalRepuestos,
                    'current_page' => 1,
                ]
            ],
            'stats' => [
                'total_repuestos' => $totalRepuestos,
                'stock_bajo' => $stockBajo,
                'valor_total_inventario' => number_format($valorTotalInventario, 2),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Repuesto/create');
    }

    public function store(StoreRepuestoRequest $request): RedirectResponse
    {
        try {
            RepuestoEloquentModel::create($request->validated());
            
            return redirect()
                ->route('repuestos.index')
                ->with('success', 'Repuesto creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el repuesto: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $repuesto = RepuestoEloquentModel::findOrFail($id);

        return Inertia::render('Repuesto/edit', [
            'repuesto' => RepuestoMapper::toDomain($repuesto)->toArray()
        ]);
    }

    public function update(UpdateRepuestoRequest $request, string $id): RedirectResponse
    {
        try {
            $repuesto = RepuestoEloquentModel::findOrFail($id);
            $repuesto->update($request->validated());

            return redirect()
                ->route('repuestos.index')
                ->with('success', 'Repuesto actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el repuesto: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return redirect()
                ->back()
                ->with('error', 'Repuesto no encontrado');
        }

        $repuesto->delete();

        return redirect()
            ->route('repuestos.index')
            ->with('success', 'Repuesto eliminado exitosamente');
    }
}
