<?php

namespace Src\OrdenReparacion\Infrastructure\Requests;

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
            'observaciones' => 'nullable|string|max:1000',
        ];
    }
}
