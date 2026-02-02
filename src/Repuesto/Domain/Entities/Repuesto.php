<?php

namespace Src\Repuesto\Domain\Entities;

use DateTimeImmutable;

class Repuesto
{
    private string $id;
    private string $codigo;
    private string $nombre;
    private ?string $descripcion;
    private string $categoria;
    private ?string $marcaCompatible;
    private ?string $modeloCompatible;
    private int $stockActual;
    private int $stockMinimo;
    private float $precioCompra;
    private float $precioVenta;
    private ?string $proveedor;
    private ?string $ubicacion;
    private string $estado;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $codigo,
        string $nombre,
        ?string $descripcion,
        string $categoria,
        ?string $marcaCompatible,
        ?string $modeloCompatible,
        int $stockActual,
        int $stockMinimo,
        float $precioCompra,
        float $precioVenta,
        ?string $proveedor,
        ?string $ubicacion,
        string $estado,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->categoria = $categoria;
        $this->marcaCompatible = $marcaCompatible;
        $this->modeloCompatible = $modeloCompatible;
        $this->stockActual = $stockActual;
        $this->stockMinimo = $stockMinimo;
        $this->precioCompra = $precioCompra;
        $this->precioVenta = $precioVenta;
        $this->proveedor = $proveedor;
        $this->ubicacion = $ubicacion;
        $this->estado = $estado;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function getMarcaCompatible(): ?string
    {
        return $this->marcaCompatible;
    }

    public function getModeloCompatible(): ?string
    {
        return $this->modeloCompatible;
    }

    public function getStockActual(): int
    {
        return $this->stockActual;
    }

    public function getStockMinimo(): int
    {
        return $this->stockMinimo;
    }

    public function getPrecioCompra(): float
    {
        return $this->precioCompra;
    }

    public function getPrecioVenta(): float
    {
        return $this->precioVenta;
    }

    public function getProveedor(): ?string
    {
        return $this->proveedor;
    }

    public function getUbicacion(): ?string
    {
        return $this->ubicacion;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function updateNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function updateDescripcion(?string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function updateCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function updateMarcaCompatible(?string $marcaCompatible): void
    {
        $this->marcaCompatible = $marcaCompatible;
    }

    public function updateModeloCompatible(?string $modeloCompatible): void
    {
        $this->modeloCompatible = $modeloCompatible;
    }

    public function updateStockActual(int $stockActual): void
    {
        $this->stockActual = $stockActual;
    }

    public function updateStockMinimo(int $stockMinimo): void
    {
        $this->stockMinimo = $stockMinimo;
    }

    public function updatePrecioCompra(float $precioCompra): void
    {
        $this->precioCompra = $precioCompra;
    }

    public function updatePrecioVenta(float $precioVenta): void
    {
        $this->precioVenta = $precioVenta;
    }

    public function updateProveedor(?string $proveedor): void
    {
        $this->proveedor = $proveedor;
    }

    public function updateUbicacion(?string $ubicacion): void
    {
        $this->ubicacion = $ubicacion;
    }

    public function updateEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'marcaCompatible' => $this->marcaCompatible,
            'modeloCompatible' => $this->modeloCompatible,
            'stockActual' => $this->stockActual,
            'stockMinimo' => $this->stockMinimo,
            'precioCompra' => $this->precioCompra,
            'precioVenta' => $this->precioVenta,
            'proveedor' => $this->proveedor,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
