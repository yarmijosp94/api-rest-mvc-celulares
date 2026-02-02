<?php

namespace Src\Repuesto\Infrastructure\Mappers;

use Src\Repuesto\Domain\Entities\Repuesto;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;

class RepuestoMapper
{
    public static function toDomain(RepuestoEloquentModel $model): Repuesto
    {
        return new Repuesto(
            id: $model->id,
            codigo: $model->codigo,
            nombre: $model->nombre,
            descripcion: $model->descripcion,
            categoria: $model->categoria,
            marcaCompatible: $model->marca_compatible,
            modeloCompatible: $model->modelo_compatible,
            stockActual: $model->stock_actual,
            stockMinimo: $model->stock_minimo,
            precioCompra: (float) $model->precio_compra,
            precioVenta: (float) $model->precio_venta,
            proveedor: $model->proveedor,
            ubicacion: $model->ubicacion,
            estado: $model->estado,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Repuesto $repuesto): array
    {
        return [
            'id' => $repuesto->getId(),
            'codigo' => $repuesto->getCodigo(),
            'nombre' => $repuesto->getNombre(),
            'descripcion' => $repuesto->getDescripcion(),
            'categoria' => $repuesto->getCategoria(),
            'marca_compatible' => $repuesto->getMarcaCompatible(),
            'modelo_compatible' => $repuesto->getModeloCompatible(),
            'stock_actual' => $repuesto->getStockActual(),
            'stock_minimo' => $repuesto->getStockMinimo(),
            'precio_compra' => $repuesto->getPrecioCompra(),
            'precio_venta' => $repuesto->getPrecioVenta(),
            'proveedor' => $repuesto->getProveedor(),
            'ubicacion' => $repuesto->getUbicacion(),
            'estado' => $repuesto->getEstado(),
        ];
    }
}
