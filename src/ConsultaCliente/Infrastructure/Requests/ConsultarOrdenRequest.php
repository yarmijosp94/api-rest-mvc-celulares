<?php

namespace Src\ConsultaCliente\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultarOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numeroOrden' => 'required|string',
            'numeroDocumento' => 'required|string'
        ];
    }

    public function attributes(): array
    {
        return [
            'numeroOrden' => 'número de orden',
            'numeroDocumento' => 'número de documento'
        ];
    }
}
