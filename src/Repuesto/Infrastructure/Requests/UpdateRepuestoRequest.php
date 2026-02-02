<?php

namespace Src\Repuesto\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->has('marcaCompatible')) {
            $data['marca_compatible'] = $this->marcaCompatible;
        }

        if ($this->has('modeloCompatible')) {
            $data['modelo_compatible'] = $this->modeloCompatible;
        }

        if ($this->has('stockActual')) {
            $data['stock_actual'] = $this->stockActual;
        }

        if ($this->has('stockMinimo')) {
            $data['stock_minimo'] = $this->stockMinimo;
        }

        if ($this->has('precioCompra')) {
            $data['precio_compra'] = $this->precioCompra;
        }

        if ($this->has('precioVenta')) {
            $data['precio_venta'] = $this->precioVenta;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        $repuestoId = $this->route('repuesto');

        return [
            'codigo' => 'required|string|unique:repuestos,codigo,' . $repuestoId,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|max:255',
            'marca_compatible' => 'nullable|string|max:255',
            'modelo_compatible' => 'nullable|string|max:255',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'estado' => 'required|in:disponible,agotado,por_pedir'
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'categoria' => 'categoría',
            'marca_compatible' => 'marca compatible',
            'modelo_compatible' => 'modelo compatible',
            'stock_actual' => 'stock actual',
            'stock_minimo' => 'stock mínimo',
            'precio_compra' => 'precio de compra',
            'precio_venta' => 'precio de venta',
            'proveedor' => 'proveedor',
            'ubicacion' => 'ubicación',
            'estado' => 'estado'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'Este código ya está registrado',
            'nombre.required' => 'El nombre es obligatorio',
            'categoria.required' => 'La categoría es obligatoria',
            'stock_actual.required' => 'El stock actual es obligatorio',
            'stock_actual.integer' => 'El stock actual debe ser un número entero',
            'stock_actual.min' => 'El stock actual no puede ser negativo',
            'stock_minimo.required' => 'El stock mínimo es obligatorio',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo',
            'precio_compra.required' => 'El precio de compra es obligatorio',
            'precio_compra.numeric' => 'El precio de compra debe ser un número',
            'precio_compra.min' => 'El precio de compra no puede ser negativo',
            'precio_venta.required' => 'El precio de venta es obligatorio',
            'precio_venta.numeric' => 'El precio de venta debe ser un número',
            'precio_venta.min' => 'El precio de venta no puede ser negativo',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser: disponible, agotado o por_pedir'
        ];
    }
}
