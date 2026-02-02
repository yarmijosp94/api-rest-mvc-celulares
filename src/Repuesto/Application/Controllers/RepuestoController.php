<?php

namespace Src\Repuesto\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Repuesto\Infrastructure\Requests\StoreRepuestoRequest;
use Src\Repuesto\Infrastructure\Requests\UpdateRepuestoRequest;
use Src\Repuesto\Infrastructure\Resources\RepuestoResource;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;

class RepuestoController extends Controller
{
    public function index()
    {
        $repuestos = RepuestoEloquentModel::all();
        return RepuestoResource::collection($repuestos);
    }

    public function store(StoreRepuestoRequest $request)
    {
        $repuesto = RepuestoEloquentModel::create($request->validated());
        return new RepuestoResource($repuesto);
    }

    public function show(string $id)
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'success' => false,
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        return new RepuestoResource($repuesto);
    }

    public function update(UpdateRepuestoRequest $request, string $id)
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'success' => false,
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        $repuesto->update($request->validated());
        return new RepuestoResource($repuesto);
    }

    public function destroy(string $id)
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'success' => false,
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        $repuesto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Repuesto eliminado exitosamente'
        ], 200);
    }
}
