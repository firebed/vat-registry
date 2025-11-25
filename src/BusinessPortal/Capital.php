<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Capital
{
    public function __construct(private array $properties)
    {
    }

    public function getCapitalStock(): ?float
    {
        return $this->properties['capitalStock'] ?? null;
    }

    public function getCurrency(): ?string
    {
        return $this->properties['currency'] ?? null;
    }

    public function getNonCapital(): ?float
    {
        return $this->properties['ecsokefalaiikes'] ?? null;
    }

    public function getBail(): ?float
    {
        return $this->properties['eggiitikes'] ?? null;
    }
}