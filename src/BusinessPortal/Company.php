<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Company
{
    public function __construct(private array $properties)
    {
    }

    public function getLegalType(): ?Enum
    {
        return !empty($this->properties['legalType']) ? new Enum($this->properties['legalType']) : null;
    }

    public function getZipCode(): ?string
    {
        return $this->properties['zipCode'] ?? null;
    }

    public function getMunicipality(): ?Enum
    {
        return !empty($this->properties['municipality']) ? new Enum($this->properties['municipality']) : null;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->properties['arGemi'] ?? null;
    }

    public function getCity(): ?string
    {
        return $this->properties['city'] ?? null;
    }

    public function getStreetNumber(): ?string
    {
        return $this->properties['streetNumber'] ?? null;
    }

    public function getTin(): ?string
    {
        return $this->properties['afm'] ?? null;
    }

    public function getPhone(): ?string
    {
        return $this->properties['phone'] ?? null;
    }

    public function getFax(): ?string
    {
        return $this->properties['fax'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->properties['url'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->properties['email'] ?? null;
    }

    public function isBranch(): bool
    {
        return filter_var($this->properties['isBranch'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getProfession(): ?string
    {
        return $this->properties['objective'] ?? null;
    }

    public function getCompanyNamesEn(): array
    {
        return $this->properties['coNamesEn'] ?? [];
    }

    public function isAutoRegistered(): bool
    {
        return filter_var($this->properties['autoRegistered'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getCompanyNameEl(): ?string
    {
        return $this->properties['coNameEl'] ?? null;
    }

    public function getLastStatusChange(): ?string
    {
        return $this->properties['lastStatusChange'] ?? null;
    }

    public function getStatus(): ?Enum
    {
        return !empty($this->properties['status']) ? new Enum($this->properties['status']) : null;
    }

    public function getTaxOffice(): ?Enum
    {
        return !empty($this->properties['prefecture']) ? new Enum($this->properties['prefecture']) : null;
    }

    public function getStreet(): ?string
    {
        return $this->properties['street'] ?? null;
    }

    public function getCommercialRegistryOffice(): ?Enum
    {
        return !empty($this->properties['gemiOffice']) ? new Enum($this->properties['gemiOffice']) : null;
    }

    /**
     * @return Activity[]
     */
    public function getActivities(): array
    {
        $activities = [];
        if (!empty($this->properties['activities']) && is_array($this->properties['activities'])) {
            foreach ($this->properties['activities'] as $activityData) {
                $activities[] = new Activity($activityData);
            }
        }
        return $activities;
    }

    public function getCommerceTitlesEl(): array
    {
        return $this->properties['coTitlesEl'] ?? [];
    }

    public function getCommerceTitlesEn(): array
    {
        return $this->properties['coTitlesEn'] ?? [];
    }

    public function getIncorporationDate(): ?string
    {
        return $this->properties['incorporationDate'] ?? null;
    }

    /**
     * @return Representative[]
     */
    public function getRepresentatives(): array
    {
        $representatives = [];
        if (!empty($this->properties['persons']) && is_array($this->properties['persons'])) {
            foreach ($this->properties['persons'] as $personData) {
                $representatives[] = new Representative($personData);
            }
        }
        return $representatives;
    }

    /**
     * @return Capital[]
     */
    public function getCapital(): array
    {
        $capitals = [];
        if (!empty($this->properties['capital']) && is_array($this->properties['capital'])) {
            foreach ($this->properties['capital'] as $capitalData) {
                $capitals[] = new Capital($capitalData);
            }
        }
        return $capitals;
    }

    /**
     * @return Stock[]
     */
    public function getStocks(): array
    {
        $stocks = [];
        if (!empty($this->properties['stocks']) && is_array($this->properties['stocks'])) {
            foreach ($this->properties['stocks'] as $stockData) {
                $stocks[] = new Stock($stockData);
            }
        }
        return $stocks;
    }

    public function getBranch(): array
    {
        return $this->properties['branch'] ?? [];
    }

    public function toArray(): array
    {
        return $this->properties;
    }
}