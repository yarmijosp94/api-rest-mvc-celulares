<?php

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechazarOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivo_rechazo' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_rechazo.required' => 'Debe especificar el motivo del rechazo',
            'motivo_rechazo.min' => 'El motivo debe tener al menos 10 caracteres',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'motivoRechazo' => $this->motivo_rechazo ?? null,
        ]);
    }
}
