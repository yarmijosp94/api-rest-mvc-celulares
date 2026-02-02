<?php

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenReparacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|uuid|exists:clientes,id',
            'equipo_id' => 'required|uuid|exists:equipos,id',
            'tecnico_id' => 'required|uuid|exists:users,id',
            'fecha_ingreso' => 'required|date',
            'fecha_promise' => 'nullable|date|after_or_equal:fecha_ingreso',
            'falla_reportada' => 'required|string|min:10',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'adelanto' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'requiere_aprobacion' => 'nullable|boolean',
            'estado' => 'nullable|in:recibido,en_diagnostico,diagnosticado,pendiente_aprobacion,aprobado,rechazado,en_reparacion,reparado,entregado,cancelado',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Asegurar que todos los campos estén disponibles como snake_case
        // El formulario Vue envía camelCase, convertimos a snake_case
        $this->merge([
            'cliente_id' => $this->clienteId ?? $this->cliente_id,
            'equipo_id' => $this->equipoId ?? $this->equipo_id,
            'tecnico_id' => $this->tecnicoId ?? $this->tecnico_id,
            'fecha_ingreso' => $this->fechaIngreso ?? $this->fecha_ingreso,
            'fecha_promise' => $this->fechaPromise ?? $this->fecha_promise,
            'falla_reportada' => $this->fallaReportada ?? $this->falla_reportada,
            'prioridad' => $this->prioridad,
            'costo_mano_obra' => $this->costoManoObra ?? $this->costo_mano_obra ?? 0,
            'adelanto' => $this->adelanto ?? null,
            'observaciones' => $this->observaciones ?? '',
            'requiere_aprobacion' => $this->requiereAprobacion ?? $this->requiere_aprobacion ?? true,
            'estado' => $this->estado ?? 'recibido',
        ]);
    }
}
