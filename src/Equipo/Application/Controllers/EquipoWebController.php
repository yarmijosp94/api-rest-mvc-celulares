<?php

namespace Src\Equipo\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Mappers\EquipoMapper;
use Src\Equipo\Infrastructure\Requests\StoreEquipoRequest;
use Src\Equipo\Infrastructure\Requests\UpdateEquipoRequest;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Mappers\ClienteMapper;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class EquipoWebController extends Controller
{
    public function index(): Response
    {
        $equipos = EquipoEloquentModel::with('cliente')->get();

        $equiposData = $equipos->map(
            fn($model) => EquipoMapper::toDomain($model)->toArray()
        )->toArray();

        return Inertia::render('Equipo/index', [
            'equipos' => [
                'data' => $equiposData,
                'links' => [],
                'meta' => [
                    'total' => count($equiposData),
                    'per_page' => count($equiposData),
                    'current_page' => 1,
                ]
            ],
            'stats' => [
                'total' => count($equiposData),
                'excelente' => $equipos->where('estado_fisico', 'Excelente')->count(),
                'bueno' => $equipos->where('estado_fisico', 'Bueno')->count(),
                'regular' => $equipos->where('estado_fisico', 'Regular')->count(),
                'malo' => $equipos->where('estado_fisico', 'Malo')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        $clientes = ClienteEloquentModel::all();
        $clientesData = $clientes->map(
            fn($model) => ClienteMapper::toDomain($model)->toArray()
        )->toArray();

        return Inertia::render('Equipo/create', [
            'clientes' => $clientesData
        ]);
    }

    public function store(StoreEquipoRequest $request): RedirectResponse
    {
        try {
            EquipoEloquentModel::create($request->validated());
            
            return redirect()
                ->route('equipos.index')
                ->with('success', 'Equipo creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el equipo: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $equipo = EquipoEloquentModel::findOrFail($id);
        $clientes = ClienteEloquentModel::all();

        $clientesData = $clientes->map(
            fn($model) => ClienteMapper::toDomain($model)->toArray()
        )->toArray();

        return Inertia::render('Equipo/edit', [
            'equipo' => EquipoMapper::toDomain($equipo)->toArray(),
            'clientes' => $clientesData
        ]);
    }

    public function update(UpdateEquipoRequest $request, string $id): RedirectResponse
    {
        try {
            $equipo = EquipoEloquentModel::findOrFail($id);
            $equipo->update($request->validated());

            return redirect()
                ->route('equipos.index')
                ->with('success', 'Equipo actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el equipo: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return redirect()
                ->back()
                ->with('error', 'Equipo no encontrado');
        }

        $equipo->delete();

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo eliminado exitosamente');
    }
}
