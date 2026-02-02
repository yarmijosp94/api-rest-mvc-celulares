<?php

namespace Src\Equipo\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Equipo\Infrastructure\Requests\StoreEquipoRequest;
use Src\Equipo\Infrastructure\Requests\UpdateEquipoRequest;
use Src\Equipo\Infrastructure\Resources\EquipoResource;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = EquipoEloquentModel::all();
        return EquipoResource::collection($equipos);
    }

    public function store(StoreEquipoRequest $request)
    {
        $equipo = EquipoEloquentModel::create($request->validated());
        return new EquipoResource($equipo);
    }

    public function show(string $id)
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return response()->json([
                'success' => false,
                'message' => 'Equipo no encontrado'
            ], 404);
        }

        return new EquipoResource($equipo);
    }

    public function update(UpdateEquipoRequest $request, string $id)
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return response()->json([
                'success' => false,
                'message' => 'Equipo no encontrado'
            ], 404);
        }

        $equipo->update($request->validated());
        return new EquipoResource($equipo);
    }

    public function destroy(string $id)
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return response()->json([
                'success' => false,
                'message' => 'Equipo no encontrado'
            ], 404);
        }

        $equipo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Equipo eliminado exitosamente'
        ], 200);
    }
}
