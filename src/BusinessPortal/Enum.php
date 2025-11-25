<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Enum
{
    public function __construct(protected array $properties)
    {
    }

    public function getId(): ?int
    {
        return $this->properties['id'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->properties['descr'] ?? null;
    }
}