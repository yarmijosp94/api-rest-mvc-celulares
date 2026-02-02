<?php

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nuevo_estado' => [
                'required',
                'in:recibido,en_diagnostico,diagnosticado,pendiente_aprobacion,aprobado,rechazado,en_reparacion,reparado,entregado,cancelado'
            ],
            'diagnostico_tecnico' => 'required_if:nuevo_estado,diagnosticado|nullable|string|min:10',
            'solucion_aplicada' => 'required_if:nuevo_estado,reparado|nullable|string|min:10',
            'motivo_rechazo' => 'required_if:nuevo_estado,rechazado|nullable|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'diagnostico_tecnico.required_if' => 'El diagnóstico técnico es obligatorio al diagnosticar una orden',
            'solucion_aplicada.required_if' => 'La solución aplicada es obligatoria al marcar como reparado',
            'motivo_rechazo.required_if' => 'El motivo de rechazo es obligatorio al rechazar una orden',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nuevoEstado' => $this->nuevo_estado ?? null,
            'diagnosticoTecnico' => $this->diagnostico_tecnico ?? null,
            'solucionAplicada' => $this->solucion_aplicada ?? null,
            'motivoRechazo' => $this->motivo_rechazo ?? null,
        ]);
    }
}
