<?php

namespace Tests;

use Firebed\VatRegistry\TaxisNet;
use PHPUnit\Framework\TestCase;

class TaxisTest extends TestCase
{
    public function test_valid_vat_number()
    {
        $env = new Env();

        $registry = new TaxisNet($env->get('GGPS_USERNAME'), $env->get('GGPS_PASSWORD'));
        $entity = $registry->handle('094014201');

        $this->assertTrue($entity->valid);
        $this->assertSame("094014201", $entity->vatNumber);
        $this->assertSame("1190", $entity->taxAuthorityId);
        $this->assertSame("ΚΕΦΟΔΕ ΑΤΤΙΚΗΣ", $entity->taxAuthorityName);
        $this->assertSame("ΜΗ ΦΠ", $entity->flagDescription);
        $this->assertSame("ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ", $entity->firmFlagDescription);
        $this->assertSame("ΕΝΕΡΓΟΣ ΑΦΜ", $entity->validityDescription);
        $this->assertSame("ΤΡΑΠΕΖΑ ΕΘΝΙΚΗ ΤΗΣ ΕΛΛΑΔΟΣ ΑΝΩΝΥΜΗ ΕΤΑΙΡΕΙΑ", $entity->legalName);
        $this->assertSame("ΑΕ", $entity->legalStatusDescription);
        $this->assertSame("ΑΙΟΛΟΥ", $entity->street);
        $this->assertSame("86", $entity->streetNumber);
        $this->assertSame("10559", $entity->postcode);
        $this->assertSame("ΑΘΗΝΑ", $entity->city);
        $this->assertSame("1900-01-01", $entity->registrationDate);
        $this->assertNull($entity->stopDate);
        $this->assertSame(true, $entity->normalVat);
        $this->assertSame(true, $entity->isCompany());
        $this->assertSame(false, $entity->isNaturalPerson());
        $this->assertSame(true, $entity->isActive());

        $this->assertCount(2, $entity->firms);

        $this->assertSame("64191204", $entity->firms[0]['code']);
        $this->assertSame("ΥΠΗΡΕΣΙΕΣ ΤΡΑΠΕΖΩΝ", $entity->firms[0]['description']);
        $this->assertSame("1", $entity->firms[0]['kind']);
        $this->assertSame("ΚΥΡΙΑ", $entity->firms[0]['kindDescription']);

        $this->assertSame("66221001", $entity->firms[1]['code']);
        $this->assertSame("ΥΠΗΡΕΣΙΕΣ ΑΣΦΑΛΙΣΤΙΚΟΥ ΠΡΑΚΤΟΡΑ ΚΑΙ ΑΣΦΑΛΙΣΤΙΚΟΥ ΣΥΜΒΟΥΛΟΥ", $entity->firms[1]['description']);
        $this->assertSame("2", $entity->firms[1]['kind']);
        $this->assertSame("ΔΕΥΤΕΡΕΥΟΥΣΑ", $entity->firms[1]['kindDescription']);
    }

    public function test_invalid_vat_number()
    {
        $env = new Env();

        $registry = new TaxisNet($env->get('GGPS_USERNAME'), $env->get('GGPS_PASSWORD'));
        $entity = $registry->handle('000000000');

        $this->assertNull($entity);
    }
}