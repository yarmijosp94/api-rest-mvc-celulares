<?php

namespace Src\ConsultaCliente\Infrastructure\Requests;

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
            'numeroDocumento' => 'required|string',
            'motivoRechazo' => 'required|string|min:10|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'motivoRechazo.required' => 'Por favor indique el motivo del rechazo',
            'motivoRechazo.min' => 'El motivo debe tener al menos 10 caracteres',
            'motivoRechazo.max' => 'El motivo no puede exceder 500 caracteres'
        ];
    }
}
