<?php

namespace Src\Equipo\Domain\Entities;

use DateTimeImmutable;

class Equipo
{
    private string $id;
    private string $clienteId;
    private string $marca;
    private string $modelo;
    private string $imei;
    private ?string $numeroSerie;
    private string $color;
    private ?string $patronBloqueo;
    private ?string $observacionesIniciales;
    private string $estadoFisico;
    private ?array $accesorios;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $clienteId,
        string $marca,
        string $modelo,
        string $imei,
        ?string $numeroSerie,
        string $color,
        ?string $patronBloqueo,
        ?string $observacionesIniciales,
        string $estadoFisico,
        ?array $accesorios,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->imei = $imei;
        $this->numeroSerie = $numeroSerie;
        $this->color = $color;
        $this->patronBloqueo = $patronBloqueo;
        $this->observacionesIniciales = $observacionesIniciales;
        $this->estadoFisico = $estadoFisico;
        $this->accesorios = $accesorios;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClienteId(): string
    {
        return $this->clienteId;
    }

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function getImei(): string
    {
        return $this->imei;
    }

    public function getNumeroSerie(): ?string
    {
        return $this->numeroSerie;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getPatronBloqueo(): ?string
    {
        return $this->patronBloqueo;
    }

    public function getObservacionesIniciales(): ?string
    {
        return $this->observacionesIniciales;
    }

    public function getEstadoFisico(): string
    {
        return $this->estadoFisico;
    }

    public function getAccesorios(): ?array
    {
        return $this->accesorios;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateMarca(string $marca): void
    {
        $this->marca = $marca;
    }

    public function updateModelo(string $modelo): void
    {
        $this->modelo = $modelo;
    }

    public function updateNumeroSerie(?string $numeroSerie): void
    {
        $this->numeroSerie = $numeroSerie;
    }

    public function updateColor(string $color): void
    {
        $this->color = $color;
    }

    public function updatePatronBloqueo(?string $patronBloqueo): void
    {
        $this->patronBloqueo = $patronBloqueo;
    }

    public function updateObservacionesIniciales(?string $observacionesIniciales): void
    {
        $this->observacionesIniciales = $observacionesIniciales;
    }

    public function updateEstadoFisico(string $estadoFisico): void
    {
        $this->estadoFisico = $estadoFisico;
    }

    public function updateAccesorios(?array $accesorios): void
    {
        $this->accesorios = $accesorios;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clienteId' => $this->clienteId,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'imei' => $this->imei,
            'numeroSerie' => $this->numeroSerie,
            'color' => $this->color,
            'patronBloqueo' => $this->patronBloqueo,
            'observacionesIniciales' => $this->observacionesIniciales,
            'estadoFisico' => $this->estadoFisico,
            'accesorios' => $this->accesorios,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
