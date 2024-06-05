<?php

namespace Firebed\VatRegistry;

class VatEntity
{
    /**
     * @var string|null ΑΦΜ
     */
    public ?string $vatNumber = null;

    /**
     * @var ?string Κωδικός ΔΟΥ
     */
    public ?string $taxAuthorityId = null;

    /**
     * @var ?string Περιγραφή ΔΟΥ
     */
    public ?string $taxAuthorityName = null;

    /**
     * @var ?string Ένδειξη εάν πρόκειται για Φυσικό Πρόσωπο ή Μη Φυσικό Πρόσωπο
     */
    public ?string $flagDescription = null;

    /**
     * @var bool true εάν ο Α.Φ.Μ. είναι ενεργός, false για απενεργοποιημένος
     */
    public ?bool $valid = null;

    /**
     * @var ?string Περιγραφή Ενεργός ή Ανενεργός
     */
    public ?string $validityDescription = null;

    /**
     * @var ?string Ένδειξη εάν πρόκειται για επιτηδευματία, μη επιτηδευματία ή πρώην επιτηδευματία
     */
    public ?string $firmFlagDescription = null;

    /**
     * @var ?string Επωνυμία Επιχείρησης
     */
    public ?string $legalName = null;

    /**
     * @var ?string Τίτλος Επιχείρησης
     */
    public ?string $commerceTitle = null;

    /**
     * @var ?string ΠΕΡΙΓΡΑΦΗ ΜΟΡΦΗΣ ΜΗ Φ.Π.
     */
    public ?string $legalStatusDescription = null;

    /**
     * @var ?string Διεύθυνση Έδρας Επιχείρησης (Οδός)
     */
    public ?string $street = null;

    /**
     * @var ?string Διεύθυνση Έδρας Επιχείρησης (Αριθμός οδού)
     */
    public ?string $streetNumber = null;

    /**
     * @var ?string Διεύθυνση Έδρας Επιχείρησης (ΤΚ)
     */
    public ?string $postcode = null;

    /**
     * @var ?string Διεύθυνση Έδρας Επιχείρησης (Πόλη)
     */
    public ?string $city = null;

    /**
     * @var ?string Ημερομηνία Έναρξης Επιχείρησης yyyy-mm-dd
     */
    public ?string $registrationDate = null;

    /**
     * @var ?string Ημερομηνία Διακοπής Επιχείρησης yyyy-mm-dd
     */
    public ?string $stopDate = null;

    /**
     * @var bool Ένδειξη Κανονικού Καθεστώτος Φ.Π.Α.
     */
    public ?bool $normalVat = null;

    /**
     * @var array Δραστηριότητες Επιχείρησης
     */
    public array $firms = [];

    public function isCompany(): bool
    {
        return $this->flagDescription === "ΜΗ ΦΠ";
    }

    public function isNaturalPerson(): bool
    {
        return $this->flagDescription === "ΦΠ";
    }

    public function isActive(): bool
    {
        return $this->firmFlagDescription === "ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ" || $this->stopDate !== null;
    }
}