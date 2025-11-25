<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Stock
{
    public function __construct(private array $properties)
    {
    }

    public function getStockTypeId(): ?int
    {
        return $this->properties['stockTypeId'] ?? null;
    }

    public function getAmount(): ?float
    {
        return $this->properties['amount'] ?? null;
    }

    public function getNominalPrice(): ?float
    {
        return $this->properties['nominalPrice'] ?? null;
    }

    public function getStockType(): ?string
    {
        return $this->properties['stockType'] ?? null;
    }
}