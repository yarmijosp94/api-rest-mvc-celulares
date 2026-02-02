<?php

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignarRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'repuesto_id' => 'required|exists:repuestos,id',
            'cantidad' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'repuesto_id.required' => 'Debe seleccionar un repuesto',
            'repuesto_id.exists' => 'El repuesto seleccionado no existe',
            'cantidad.required' => 'Debe especificar la cantidad',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'repuestoId' => $this->repuesto_id ?? null,
        ]);
    }
}
