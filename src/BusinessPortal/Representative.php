<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Representative
{
    public function __construct(private array $properties)
    {
    }

    public function getName(): ?string
    {
        return $this->properties['personName'] ?? null;
    }

    public function getBusinessName(): ?string
    {
        return $this->properties['businessName'] ?? null;
    }

    public function getRole(): ?string
    {
        return $this->properties['role'] ?? null;
    }

    public function getDateFrom(): ?string
    {
        return $this->properties['dtFrom'] ?? null;
    }

    public function getDateTo(): ?string
    {
        return $this->properties['dtTo'] ?? null;
    }

    public function isRepresentativeAlone(): ?bool
    {
        return filter_var($this->properties['isRepresentativeAlone'] ?? null, FILTER_VALIDATE_BOOLEAN);
    }

    public function isRepresentativeInCommon(): ?bool
    {
        return filter_var($this->properties['isRepresentativeInCommon'] ?? null, FILTER_VALIDATE_BOOLEAN);
    }

    public function getPercentage(): ?string
    {
        return $this->properties['percentage'] ?? null;
    }

    public function getCategory(): ?string
    {
        return $this->properties['category'] ?? null;
    }
}