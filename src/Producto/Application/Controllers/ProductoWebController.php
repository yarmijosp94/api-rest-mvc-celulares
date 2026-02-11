<?php

namespace Src\Producto\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Producto\Infrastructure\Models\ProductoEloquentModel;
use Src\Producto\Infrastructure\Mappers\ProductoMapper;
use Src\Producto\Infrastructure\Requests\StoreProductoRequest;
use Src\Producto\Infrastructure\Requests\UpdateProductoRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class ProductoWebController extends Controller
{
    public function index(): Response
    {
        $productos = ProductoEloquentModel::with('categoria')->get();

        $productosData = $productos->map(
            fn($model) => ProductoMapper::toDomain($model)->toArray()
        )->toArray();

        $totalProductos = count($productosData);
        $stockBajo = $productos->filter(
            fn($producto) => $producto->stock < 10
        )->count();

        $valorTotalInventario = $productos->sum(function ($producto) {
            return $producto->stock * $producto->precio_unitario;
        });

        return Inertia::render('Producto/index', [
            'productos' => [
                'data' => $productosData,
                'links' => [],
                'meta' => [
                    'total' => $totalProductos,
                    'per_page' => $totalProductos,
                    'current_page' => 1,
                ]
            ],
            'stats' => [
                'total_productos' => $totalProductos,
                'stock_bajo' => $stockBajo,
                'valor_total_inventario' => number_format($valorTotalInventario, 2),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Producto/create');
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        try {
            ProductoEloquentModel::create($request->validated());
            
            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $producto = ProductoEloquentModel::with('categoria')->findOrFail($id);

        return Inertia::render('Producto/edit', [
            'producto' => ProductoMapper::toDomain($producto)->toArray()
        ]);
    }

    public function update(UpdateProductoRequest $request, string $id): RedirectResponse
    {
        try {
            $producto = ProductoEloquentModel::findOrFail($id);
            $producto->update($request->validated());

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $producto = ProductoEloquentModel::find($id);

        if (!$producto) {
            return redirect()
                ->back()
                ->with('error', 'Producto no encontrado');
        }

        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
