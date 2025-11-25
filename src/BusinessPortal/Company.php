<?php

namespace Firebed\VatRegistry\BusinessPortal;

class Company
{
    public function __construct(private array $data)
    {
    }

    public function getLegalType(): ?Enum
    {
        return !empty($this->data['legalType']) ? new Enum($this->data['legalType']) : null;
    }

    public function getZipCode(): ?string
    {
        return $this->data['zipCode'] ?? null;
    }

    public function getMunicipality(): ?Enum
    {
        return !empty($this->data['municipality']) ? new Enum($this->data['municipality']) : null;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->data['arGemi'] ?? null;
    }

    public function getCity(): ?string
    {
        return $this->data['city'] ?? null;
    }

    public function getStreetNumber(): ?string
    {
        return $this->data['streetNumber'] ?? null;
    }

    public function getTin(): ?string
    {
        return $this->data['afm'] ?? null;
    }

    public function getPhone(): ?string
    {
        return $this->data['phone'] ?? null;
    }

    public function getFax(): ?string
    {
        return $this->data['fax'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->data['url'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    public function isBranch(): bool
    {
        return filter_var($this->data['isBranch'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getObjective(): ?string
    {
        return $this->data['objective'] ?? null;
    }

    public function getCompanyNamesEn(): array
    {
        return $this->data['coNamesEn'] ?? [];
    }

    public function isAutoRegistered(): bool
    {
        return filter_var($this->data['autoRegistered'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getPostalCode(): ?string
    {
        return $this->data['poBox'] ?? null;
    }

    public function getCompanyNameEl(): ?string
    {
        return $this->data['coNameEl'] ?? null;
    }

    public function getLastStatusChange(): ?string
    {
        return $this->data['lastStatusChange'] ?? null;
    }

    public function getStatus(): ?Enum
    {
        return !empty($this->data['status']) ? new Enum($this->data['status']) : null;
    }

    public function getTaxOffice(): ?Enum
    {
        return !empty($this->data['prefecture']) ? new Enum($this->data['prefecture']) : null;
    }

    public function getStreet(): ?string
    {
        return $this->data['street'] ?? null;
    }

    public function getCommercialRegistryOffice(): ?Enum
    {
        return !empty($this->data['gemiOffice']) ? new Enum($this->data['gemiOffice']) : null;
    }

    /**
     * @return Activity[]
     */
    public function getActivities(): array
    {
        $activities = [];
        if (!empty($this->data['activities']) && is_array($this->data['activities'])) {
            foreach ($this->data['activities'] as $activityData) {
                $activities[] = new Activity($activityData);
            }
        }
        return $activities;
    }

    public function getCompanyTitlesEl(): array
    {
        return $this->data['coTitlesEl'] ?? [];
    }

    public function getCompanyTitlesEn(): array
    {
        return $this->data['coTitlesEn'] ?? [];
    }

    public function getIncorporationDate(): ?string
    {
        return $this->data['incorporationDate'] ?? null;
    }

    /**
     * @return Representative[]
     */
    public function getRepresentatives(): array
    {
        $representatives = [];
        if (!empty($this->data['persons']) && is_array($this->data['persons'])) {
            foreach ($this->data['persons'] as $personData) {
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
        if (!empty($this->data['capital']) && is_array($this->data['capital'])) {
            foreach ($this->data['capital'] as $capitalData) {
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
        if (!empty($this->data['stocks']) && is_array($this->data['stocks'])) {
            foreach ($this->data['stocks'] as $stockData) {
                $stocks[] = new Stock($stockData);
            }
        }
        return $stocks;
    }

    public function getBranch(): array
    {
        return $this->data['branch'] ?? [];
    }
}