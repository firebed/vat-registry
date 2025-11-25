<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Activity extends Enum
{
    public function __construct(private array $data)
    {
        parent::__construct($data);
    }

    public function getDateFrom(): ?string
    {
        return $this->data['dtFrom'] ?? null;
    }

    public function getDateTo(): ?string
    {
        return $this->data['dtTo'] ?? null;
    }

    public function getType(): ?string
    {
        return $this->data['type'] ?? null;
    }

    public function isPrimary(): bool
    {
        return $this->getType() === 'Κύρια';
    }
}