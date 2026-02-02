<?php

namespace Src\Equipo\Infrastructure\Mappers;

use Src\Equipo\Domain\Entities\Equipo;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;

class EquipoMapper
{
    public static function toDomain(EquipoEloquentModel $model): Equipo
    {
        return new Equipo(
            id: $model->id,
            clienteId: $model->cliente_id,
            marca: $model->marca,
            modelo: $model->modelo,
            imei: $model->imei,
            numeroSerie: $model->numero_serie,
            color: $model->color,
            patronBloqueo: $model->patron_bloqueo,
            observacionesIniciales: $model->observaciones_iniciales,
            estadoFisico: $model->estado_fisico,
            accesorios: $model->accesorios,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Equipo $equipo): array
    {
        return [
            'id' => $equipo->getId(),
            'cliente_id' => $equipo->getClienteId(),
            'marca' => $equipo->getMarca(),
            'modelo' => $equipo->getModelo(),
            'imei' => $equipo->getImei(),
            'numero_serie' => $equipo->getNumeroSerie(),
            'color' => $equipo->getColor(),
            'patron_bloqueo' => $equipo->getPatronBloqueo(),
            'observaciones_iniciales' => $equipo->getObservacionesIniciales(),
            'estado_fisico' => $equipo->getEstadoFisico(),
            'accesorios' => $equipo->getAccesorios(),
        ];
    }
}
