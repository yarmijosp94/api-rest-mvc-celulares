<?php

namespace Src\Tecnico\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTecnicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'especialidad' => 'nullable|string|max:255',
            'certificacion' => 'nullable|string|max:255',
            'fecha_contratacion' => 'nullable|date|before_or_equal:today',
            'activo' => 'boolean'
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'especialidad' => 'especialidad',
            'certificacion' => 'certificación',
            'fecha_contratacion' => 'fecha de contratación',
            'activo' => 'estado activo'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'email.email' => 'El correo electrónico no es válido',
            'fecha_contratacion.before_or_equal' => 'La fecha de contratación no puede ser futura'
        ];
    }
}
