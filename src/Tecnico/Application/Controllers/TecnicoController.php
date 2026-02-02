<?php

namespace Src\Tecnico\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Tecnico\Infrastructure\Requests\StoreTecnicoRequest;
use Src\Tecnico\Infrastructure\Requests\UpdateTecnicoRequest;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;

class TecnicoController extends Controller
{
    public function index()
    {
        $tecnicos = TecnicoEloquentModel::with('user')->get();
        return TecnicoResource::collection($tecnicos);
    }

    public function store(StoreTecnicoRequest $request)
    {
        $tecnico = TecnicoEloquentModel::create($request->validated());
        return new TecnicoResource($tecnico->load('user'));
    }

    public function show(string $id)
    {
        $tecnico = TecnicoEloquentModel::with('user')->find($id);

        if (!$tecnico) {
            return response()->json([
                'success' => false,
                'message' => 'Técnico no encontrado'
            ], 404);
        }

        return new TecnicoResource($tecnico);
    }

    public function update(UpdateTecnicoRequest $request, string $id)
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return response()->json([
                'success' => false,
                'message' => 'Técnico no encontrado'
            ], 404);
        }

        $tecnico->update($request->validated());
        return new TecnicoResource($tecnico->load('user'));
    }

    public function destroy(string $id)
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return response()->json([
                'success' => false,
                'message' => 'Técnico no encontrado'
            ], 404);
        }

        $tecnico->delete();

        return response()->json([
            'success' => true,
            'message' => 'Técnico eliminado exitosamente'
        ], 200);
    }
}
