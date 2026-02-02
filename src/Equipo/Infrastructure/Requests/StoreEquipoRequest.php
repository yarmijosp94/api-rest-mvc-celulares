<?php

namespace Src\Equipo\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->has('clienteId')) {
            $data['cliente_id'] = $this->clienteId;
        }

        if ($this->has('numeroSerie')) {
            $data['numero_serie'] = $this->numeroSerie;
        }

        if ($this->has('patronBloqueo')) {
            $data['patron_bloqueo'] = $this->patronBloqueo;
        }

        if ($this->has('observacionesIniciales')) {
            $data['observaciones_iniciales'] = $this->observacionesIniciales;
        }

        if ($this->has('estadoFisico')) {
            $data['estado_fisico'] = $this->estadoFisico;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|uuid|exists:clientes,id',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'imei' => 'required|string|unique:equipos,imei',
            'numero_serie' => 'nullable|string|max:255',
            'color' => 'required|string|max:255',
            'patron_bloqueo' => 'nullable|string',
            'observaciones_iniciales' => 'nullable|string',
            'estado_fisico' => 'required|in:Excelente,Bueno,Regular,Malo',
            'accesorios' => 'nullable|array'
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'imei' => 'IMEI',
            'numero_serie' => 'número de serie',
            'color' => 'color',
            'patron_bloqueo' => 'patrón de bloqueo',
            'observaciones_iniciales' => 'observaciones iniciales',
            'estado_fisico' => 'estado físico',
            'accesorios' => 'accesorios'
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'marca.required' => 'La marca es obligatoria',
            'modelo.required' => 'El modelo es obligatorio',
            'imei.required' => 'El IMEI es obligatorio',
            'imei.unique' => 'Este IMEI ya está registrado',
            'color.required' => 'El color es obligatorio',
            'estado_fisico.required' => 'El estado físico es obligatorio',
            'estado_fisico.in' => 'El estado físico debe ser: Excelente, Bueno, Regular o Malo'
        ];
    }
}
