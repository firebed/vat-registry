<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Activity
{
    public function __construct(protected array $properties)
    {
    }

    public function getId(): ?int
    {
        return $this->properties['activity']['id'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->properties['activity']['descr'] ?? null;
    }

    public function getDateFrom(): ?string
    {
        return $this->properties['dtFrom'] ?? null;
    }

    public function getDateTo(): ?string
    {
        return $this->properties['dtTo'] ?? null;
    }

    public function getType(): ?string
    {
        return $this->properties['type'] ?? null;
    }

    public function isPrimary(): bool
    {
        return $this->getType() === 'Κύρια';
    }
}
