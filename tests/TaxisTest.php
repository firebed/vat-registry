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

        // Secondary activities change over time in the live registry, so only the primary one is pinned
        foreach ($entity->firms as $firm) {
            $this->assertSame(['code', 'description', 'kind', 'kindDescription'], array_keys($firm));
            $this->assertNotContains('', $firm);
        }

        $primary = array_values(array_filter($entity->firms, fn (array $firm) => $firm['kind'] === '1'));
        $this->assertSame([[
            'code'            => '64191204',
            'description'     => 'ΥΠΗΡΕΣΙΕΣ ΤΡΑΠΕΖΩΝ',
            'kind'            => '1',
            'kindDescription' => 'ΚΥΡΙΑ',
        ]], $primary);

        $this->assertContains('ΔΕΥΤΕΡΕΥΟΥΣΑ', array_column($entity->firms, 'kindDescription'));
    }

    public function test_invalid_vat_number()
    {
        $env = new Env();

        $registry = new TaxisNet($env->get('GGPS_USERNAME'), $env->get('GGPS_PASSWORD'));
        $entity = $registry->handle('000000000');

        $this->assertNull($entity);
    }
}