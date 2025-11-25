<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Activity extends Enum
{
    public function __construct(protected array $properties)
    {
        parent::__construct($this->properties['activity'] ?? []);
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