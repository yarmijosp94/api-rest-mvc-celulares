<?php

namespace Src\Tecnico\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTecnicoRequest extends FormRequest
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
            'user_id' => 'sometimes|uuid|exists:users,id|unique:tecnicos,user_id,' . $this->route('tecnico'),
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
            'fecha_contratacion.before_or_equal' => 'La fecha de contratación no puede ser futura'
        ];
    }
}
