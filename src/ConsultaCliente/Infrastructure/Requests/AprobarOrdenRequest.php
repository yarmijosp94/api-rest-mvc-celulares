<?php

namespace Src\ConsultaCliente\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AprobarOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numeroDocumento' => 'required|string'
        ];
    }
}
