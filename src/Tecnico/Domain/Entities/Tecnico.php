<?php

namespace Src\Tecnico\Domain\Entities;

use DateTimeImmutable;

class Tecnico
{
    private string $id;
    private string $nombre;
    private ?string $telefono;
    private ?string $email;
    private ?string $especialidad;
    private ?string $certificacion;
    private ?DateTimeImmutable $fechaContratacion;
    private bool $activo;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $nombre,
        ?string $telefono,
        ?string $email,
        ?string $especialidad,
        ?string $certificacion,
        ?DateTimeImmutable $fechaContratacion,
        bool $activo,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->especialidad = $especialidad;
        $this->certificacion = $certificacion;
        $this->fechaContratacion = $fechaContratacion;
        $this->activo = $activo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getEspecialidad(): ?string
    {
        return $this->especialidad;
    }

    public function getCertificacion(): ?string
    {
        return $this->certificacion;
    }

    public function getFechaContratacion(): ?DateTimeImmutable
    {
        return $this->fechaContratacion;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function updateTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function updateEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function updateEspecialidad(?string $especialidad): void
    {
        $this->especialidad = $especialidad;
    }

    public function updateCertificacion(?string $certificacion): void
    {
        $this->certificacion = $certificacion;
    }

    public function updateFechaContratacion(?DateTimeImmutable $fechaContratacion): void
    {
        $this->fechaContratacion = $fechaContratacion;
    }

    public function updateActivo(bool $activo): void
    {
        $this->activo = $activo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'especialidad' => $this->especialidad,
            'certificacion' => $this->certificacion,
            'fechaContratacion' => $this->fechaContratacion?->format('Y-m-d'),
            'activo' => $this->activo,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
