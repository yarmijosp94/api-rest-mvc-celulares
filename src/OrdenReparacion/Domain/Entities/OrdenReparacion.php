<?php

namespace Src\OrdenReparacion\Domain\Entities;

use DateTimeImmutable;

class OrdenReparacion
{
    private string $id;
    private string $numeroOrden;
    private string $clienteId;
    private string $equipoId;
    private string $tecnicoId;
    private DateTimeImmutable $fechaIngreso;
    private ?DateTimeImmutable $fechaPromesa;
    private ?DateTimeImmutable $fechaEntrega;
    private string $fallaReportada;
    private ?string $diagnosticoTecnico;
    private ?string $solucionAplicada;
    private string $estado;
    private string $prioridad;
    private float $costoManoObra;
    private float $costoRepuestos;
    private float $costoTotal;
    private ?float $adelanto;
    private ?string $observaciones;
    private bool $requiereAprobacion;
    private ?bool $aprobadoPorCliente;
    private ?DateTimeImmutable $fechaAprobacion;
    private ?string $motivoRechazo;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $numeroOrden,
        string $clienteId,
        string $equipoId,
        string $tecnicoId,
        DateTimeImmutable $fechaIngreso,
        ?DateTimeImmutable $fechaPromesa,
        ?DateTimeImmutable $fechaEntrega,
        string $fallaReportada,
        ?string $diagnosticoTecnico,
        ?string $solucionAplicada,
        string $estado,
        string $prioridad,
        float $costoManoObra,
        float $costoRepuestos,
        float $costoTotal,
        ?float $adelanto,
        ?string $observaciones,
        bool $requiereAprobacion,
        ?bool $aprobadoPorCliente,
        ?DateTimeImmutable $fechaAprobacion,
        ?string $motivoRechazo,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->numeroOrden = $numeroOrden;
        $this->clienteId = $clienteId;
        $this->equipoId = $equipoId;
        $this->tecnicoId = $tecnicoId;
        $this->fechaIngreso = $fechaIngreso;
        $this->fechaPromesa = $fechaPromesa;
        $this->fechaEntrega = $fechaEntrega;
        $this->fallaReportada = $fallaReportada;
        $this->diagnosticoTecnico = $diagnosticoTecnico;
        $this->solucionAplicada = $solucionAplicada;
        $this->estado = $estado;
        $this->prioridad = $prioridad;
        $this->costoManoObra = $costoManoObra;
        $this->costoRepuestos = $costoRepuestos;
        $this->costoTotal = $costoTotal;
        $this->adelanto = $adelanto;
        $this->observaciones = $observaciones;
        $this->requiereAprobacion = $requiereAprobacion;
        $this->aprobadoPorCliente = $aprobadoPorCliente;
        $this->fechaAprobacion = $fechaAprobacion;
        $this->motivoRechazo = $motivoRechazo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNumeroOrden(): string
    {
        return $this->numeroOrden;
    }

    public function getClienteId(): string
    {
        return $this->clienteId;
    }

    public function getEquipoId(): string
    {
        return $this->equipoId;
    }

    public function getTecnicoId(): string
    {
        return $this->tecnicoId;
    }

    public function getFechaIngreso(): DateTimeImmutable
    {
        return $this->fechaIngreso;
    }

    public function getFechaPromesa(): ?DateTimeImmutable
    {
        return $this->fechaPromesa;
    }

    public function getFechaEntrega(): ?DateTimeImmutable
    {
        return $this->fechaEntrega;
    }

    public function getFallaReportada(): string
    {
        return $this->fallaReportada;
    }

    public function getDiagnosticoTecnico(): ?string
    {
        return $this->diagnosticoTecnico;
    }

    public function getSolucionAplicada(): ?string
    {
        return $this->solucionAplicada;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getPrioridad(): string
    {
        return $this->prioridad;
    }

    public function getCostoManoObra(): float
    {
        return $this->costoManoObra;
    }

    public function getCostoRepuestos(): float
    {
        return $this->costoRepuestos;
    }

    public function getCostoTotal(): float
    {
        return $this->costoTotal;
    }

    public function getAdelanto(): ?float
    {
        return $this->adelanto;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getRequiereAprobacion(): bool
    {
        return $this->requiereAprobacion;
    }

    public function getAprobadoPorCliente(): ?bool
    {
        return $this->aprobadoPorCliente;
    }

    public function getFechaAprobacion(): ?DateTimeImmutable
    {
        return $this->fechaAprobacion;
    }

    public function getMotivoRechazo(): ?string
    {
        return $this->motivoRechazo;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateNumeroOrden(string $numeroOrden): void
    {
        $this->numeroOrden = $numeroOrden;
    }

    public function updateClienteId(string $clienteId): void
    {
        $this->clienteId = $clienteId;
    }

    public function updateEquipoId(string $equipoId): void
    {
        $this->equipoId = $equipoId;
    }

    public function updateTecnicoId(string $tecnicoId): void
    {
        $this->tecnicoId = $tecnicoId;
    }

    public function updateFechaIngreso(DateTimeImmutable $fechaIngreso): void
    {
        $this->fechaIngreso = $fechaIngreso;
    }

    public function updateFechaPromesa(?DateTimeImmutable $fechaPromesa): void
    {
        $this->fechaPromesa = $fechaPromesa;
    }

    public function updateFechaEntrega(?DateTimeImmutable $fechaEntrega): void
    {
        $this->fechaEntrega = $fechaEntrega;
    }

    public function updateFallaReportada(string $fallaReportada): void
    {
        $this->fallaReportada = $fallaReportada;
    }

    public function updateDiagnosticoTecnico(?string $diagnosticoTecnico): void
    {
        $this->diagnosticoTecnico = $diagnosticoTecnico;
    }

    public function updateSolucionAplicada(?string $solucionAplicada): void
    {
        $this->solucionAplicada = $solucionAplicada;
    }

    public function updateEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function updatePrioridad(string $prioridad): void
    {
        $this->prioridad = $prioridad;
    }

    public function updateCostoManoObra(float $costoManoObra): void
    {
        $this->costoManoObra = $costoManoObra;
    }

    public function updateCostoRepuestos(float $costoRepuestos): void
    {
        $this->costoRepuestos = $costoRepuestos;
    }

    public function updateCostoTotal(float $costoTotal): void
    {
        $this->costoTotal = $costoTotal;
    }

    public function updateAdelanto(?float $adelanto): void
    {
        $this->adelanto = $adelanto;
    }

    public function updateObservaciones(?string $observaciones): void
    {
        $this->observaciones = $observaciones;
    }

    public function updateRequiereAprobacion(bool $requiereAprobacion): void
    {
        $this->requiereAprobacion = $requiereAprobacion;
    }

    public function updateAprobadoPorCliente(?bool $aprobadoPorCliente): void
    {
        $this->aprobadoPorCliente = $aprobadoPorCliente;
    }

    public function updateFechaAprobacion(?DateTimeImmutable $fechaAprobacion): void
    {
        $this->fechaAprobacion = $fechaAprobacion;
    }

    public function updateMotivoRechazo(?string $motivoRechazo): void
    {
        $this->motivoRechazo = $motivoRechazo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'numeroOrden' => $this->numeroOrden,
            'clienteId' => $this->clienteId,
            'equipoId' => $this->equipoId,
            'tecnicoId' => $this->tecnicoId,
            'fechaIngreso' => $this->fechaIngreso->format('Y-m-d H:i:s'),
            'fechaPromesa' => $this->fechaPromesa?->format('Y-m-d H:i:s'),
            'fechaEntrega' => $this->fechaEntrega?->format('Y-m-d H:i:s'),
            'fallaReportada' => $this->fallaReportada,
            'diagnosticoTecnico' => $this->diagnosticoTecnico,
            'solucionAplicada' => $this->solucionAplicada,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'costoManoObra' => $this->costoManoObra,
            'costoRepuestos' => $this->costoRepuestos,
            'costoTotal' => $this->costoTotal,
            'adelanto' => $this->adelanto,
            'observaciones' => $this->observaciones,
            'requiereAprobacion' => $this->requiereAprobacion,
            'aprobadoPorCliente' => $this->aprobadoPorCliente,
            'fechaAprobacion' => $this->fechaAprobacion?->format('Y-m-d H:i:s'),
            'motivoRechazo' => $this->motivoRechazo,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
