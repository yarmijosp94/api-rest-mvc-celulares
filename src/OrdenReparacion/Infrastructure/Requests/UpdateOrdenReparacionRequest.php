<?php

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenReparacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'sometimes|exists:clientes,id',
            'equipo_id' => 'sometimes|exists:equipos,id',
            'tecnico_id' => 'sometimes|exists:users,id',
            'fecha_ingreso' => 'sometimes|date',
            'fecha_promesa' => 'nullable|date',
            'falla_reportada' => 'sometimes|string|min:10',
            'diagnostico_tecnico' => 'nullable|string',
            'solucion_aplicada' => 'nullable|string',
            'prioridad' => 'sometimes|in:baja,media,alta,urgente',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'adelanto' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'requiere_aprobacion' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $updates = [];
        
        if ($this->has('cliente_id')) $updates['clienteId'] = $this->cliente_id;
        if ($this->has('equipo_id')) $updates['equipoId'] = $this->equipo_id;
        if ($this->has('tecnico_id')) $updates['tecnicoId'] = $this->tecnico_id;
        if ($this->has('fecha_ingreso')) $updates['fechaIngreso'] = $this->fecha_ingreso;
        if ($this->has('fecha_promesa')) $updates['fechaPromesa'] = $this->fecha_promesa;
        if ($this->has('falla_reportada')) $updates['fallaReportada'] = $this->falla_reportada;
        if ($this->has('diagnostico_tecnico')) $updates['diagnosticoTecnico'] = $this->diagnostico_tecnico;
        if ($this->has('solucion_aplicada')) $updates['solucionAplicada'] = $this->solucion_aplicada;
        if ($this->has('costo_mano_obra')) $updates['costoManoObra'] = $this->costo_mano_obra;
        if ($this->has('requiere_aprobacion')) $updates['requiereAprobacion'] = $this->requiere_aprobacion;
        
        $this->merge($updates);
    }
}
