<?php

namespace Src\Tecnico\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Tecnico\Infrastructure\Requests\StoreTecnicoRequest;
use Src\Tecnico\Infrastructure\Requests\UpdateTecnicoRequest;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Mappers\TecnicoMapper;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class TecnicoWebController extends Controller
{
    public function index(): Response
    {
        $tecnicos = TecnicoEloquentModel::with('user')->get();
        $tecnicosData = $tecnicos->map(
            fn($model) => TecnicoMapper::toDomain($model)->toArray()
        )->toArray();

        $stats = [
            'total' => count($tecnicosData),
            'activos' => count(array_filter($tecnicosData, fn($t) => $t['activo'])),
            'inactivos' => count(array_filter($tecnicosData, fn($t) => !$t['activo']))
        ];

        return Inertia::render('Tecnico/index', [
            'tecnicos' => [
                'data' => $tecnicosData,
                'links' => [],
                'meta' => [
                    'total' => count($tecnicosData),
                    'per_page' => count($tecnicosData),
                    'current_page' => 1
                ]
            ],
            'stats' => $stats
        ]);
    }

    public function create(): Response
    {
        $usuarios = UserEloquentModel::all();
        $usuariosData = $usuarios->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email
        ])->toArray();

        return Inertia::render('Tecnico/create', [
            'usuarios' => $usuariosData
        ]);
    }

    public function store(StoreTecnicoRequest $request): RedirectResponse
    {
        try {
            TecnicoEloquentModel::create($request->validated());

            return redirect()
                ->route('tecnicos.index')
                ->with('success', 'Técnico creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el técnico: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $tecnico = TecnicoEloquentModel::findOrFail($id);

        return Inertia::render('Tecnico/edit', [
            'tecnico' => TecnicoMapper::toDomain($tecnico)->toArray(),
            'usuarios' => UserEloquentModel::all()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email
            ])->toArray()
        ]);
    }

    public function update(UpdateTecnicoRequest $request, string $id): RedirectResponse
    {
        try {
            $tecnico = TecnicoEloquentModel::findOrFail($id);
            $tecnico->update($request->validated());

            return redirect()
                ->route('tecnicos.index')
                ->with('success', 'Técnico actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el técnico: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return redirect()
                ->back()
                ->with('error', 'Técnico no encontrado');
        }

        $tecnico->delete();

        return redirect()
            ->route('tecnicos.index')
            ->with('success', 'Técnico eliminado exitosamente');
    }
}
