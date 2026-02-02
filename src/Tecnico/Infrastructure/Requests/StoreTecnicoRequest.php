<?php

namespace Src\Tecnico\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTecnicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Soporta tanto camelCase (API) como snake_case (formularios web)
        $this->merge([
            'user_id' => $this->user_id ?? $this->userId,
            'fecha_contratacion' => $this->fecha_contratacion ?? $this->fechaContratacion,
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id|unique:tecnicos,user_id',
            'especialidad' => 'nullable|string|max:255',
            'certificacion' => 'nullable|string|max:255',
            'fecha_contratacion' => 'nullable|date|before_or_equal:today',
            'activo' => 'boolean'
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'usuario',
            'especialidad' => 'especialidad',
            'certificacion' => 'certificación',
            'fecha_contratacion' => 'fecha de contratación',
            'activo' => 'estado activo'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario no existe',
            'user_id.unique' => 'Este usuario ya está registrado como técnico',
            'fecha_contratacion.before_or_equal' => 'La fecha de contratación no puede ser futura'
        ];
    }
}
