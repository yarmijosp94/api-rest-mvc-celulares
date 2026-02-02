<?php

namespace Src\OrdenReparacion\Infrastructure\Mappers;

use Src\OrdenReparacion\Domain\Entities\OrdenReparacion;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use DateTimeImmutable;

class OrdenReparacionMapper
{
    public static function toDomain(OrdenReparacionEloquentModel $model): OrdenReparacion
    {
        return new OrdenReparacion(
            id: $model->id,
            numeroOrden: $model->numero_orden,
            clienteId: $model->cliente_id,
            equipoId: $model->equipo_id,
            tecnicoId: $model->tecnico_id,
            fechaIngreso: new DateTimeImmutable($model->fecha_ingreso->toDateTimeString()),
            fechaPromesa: $model->fecha_promesa ? new DateTimeImmutable($model->fecha_promesa->toDateTimeString()) : null,
            fechaEntrega: $model->fecha_entrega ? new DateTimeImmutable($model->fecha_entrega->toDateTimeString()) : null,
            fallaReportada: $model->falla_reportada,
            diagnosticoTecnico: $model->diagnostico_tecnico,
            solucionAplicada: $model->solucion_aplicada,
            estado: $model->estado,
            prioridad: $model->prioridad,
            costoManoObra: (float) $model->costo_mano_obra,
            costoRepuestos: (float) $model->costo_repuestos,
            costoTotal: (float) $model->costo_total,
            adelanto: $model->adelanto ? (float) $model->adelanto : null,
            observaciones: $model->observaciones,
            requiereAprobacion: $model->requiere_aprobacion,
            aprobadoPorCliente: $model->aprobado_por_cliente,
            fechaAprobacion: $model->fecha_aprobacion ? new DateTimeImmutable($model->fecha_aprobacion->toDateTimeString()) : null,
            motivoRechazo: $model->motivo_rechazo,
            createdAt: new DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(OrdenReparacion $orden): array
    {
        return [
            'id' => $orden->getId(),
            'numero_orden' => $orden->getNumeroOrden(),
            'cliente_id' => $orden->getClienteId(),
            'equipo_id' => $orden->getEquipoId(),
            'tecnico_id' => $orden->getTecnicoId(),
            'fecha_ingreso' => $orden->getFechaIngreso()->format('Y-m-d H:i:s'),
            'fecha_promesa' => $orden->getFechaPromesa()?->format('Y-m-d H:i:s'),
            'fecha_entrega' => $orden->getFechaEntrega()?->format('Y-m-d H:i:s'),
            'falla_reportada' => $orden->getFallaReportada(),
            'diagnostico_tecnico' => $orden->getDiagnosticoTecnico(),
            'solucion_aplicada' => $orden->getSolucionAplicada(),
            'estado' => $orden->getEstado(),
            'prioridad' => $orden->getPrioridad(),
            'costo_mano_obra' => $orden->getCostoManoObra(),
            'costo_repuestos' => $orden->getCostoRepuestos(),
            'costo_total' => $orden->getCostoTotal(),
            'adelanto' => $orden->getAdelanto(),
            'observaciones' => $orden->getObservaciones(),
            'requiere_aprobacion' => $orden->getRequiereAprobacion(),
            'aprobado_por_cliente' => $orden->getAprobadoPorCliente(),
            'fecha_aprobacion' => $orden->getFechaAprobacion()?->format('Y-m-d H:i:s'),
            'motivo_rechazo' => $orden->getMotivoRechazo(),
        ];
    }
}
