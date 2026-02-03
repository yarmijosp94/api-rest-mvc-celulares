<?php

namespace Src\Tecnico\Infrastructure\Mappers;

use Src\Tecnico\Domain\Entities\Tecnico;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;

class TecnicoMapper
{
    public static function toDomain(TecnicoEloquentModel $model): Tecnico
    {
        return new Tecnico(
            id: $model->id,
            nombre: $model->nombre,
            telefono: $model->telefono,
            email: $model->email,
            especialidad: $model->especialidad,
            certificacion: $model->certificacion,
            fechaContratacion: $model->fecha_contratacion
                ? new \DateTimeImmutable($model->fecha_contratacion->toDateString())
                : null,
            activo: $model->activo,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public static function toEloquent(Tecnico $tecnico): array
    {
        return [
            'id' => $tecnico->getId(),
            'nombre' => $tecnico->getNombre(),
            'telefono' => $tecnico->getTelefono(),
            'email' => $tecnico->getEmail(),
            'especialidad' => $tecnico->getEspecialidad(),
            'certificacion' => $tecnico->getCertificacion(),
            'fecha_contratacion' => $tecnico->getFechaContratacion()?->format('Y-m-d'),
            'activo' => $tecnico->isActivo(),
        ];
    }
}
