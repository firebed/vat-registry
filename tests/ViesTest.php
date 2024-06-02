<?php

namespace Tests;

use Firebed\VatRegistry\VIES;
use PHPUnit\Framework\TestCase;

class ViesTest extends TestCase
{
    public function test_valid_vat_number()
    {
        $vies = new VIES();
        $entity = $vies->handle('EL', '094014201');
        
        $this->assertTrue($entity->valid);
        $this->assertSame("094014201", $entity->vatNumber);
        $this->assertSame("ΤΡΑΠΕΖΑ ΕΘΝΙΚΗ ΤΗΣ ΕΛΛΑΔΟΣ ΑΝΩΝΥΜΗ ΕΤΑΙΡΕΙΑ", $entity->legalName);
        $this->assertSame("ΑΙΟΛΟΥ", $entity->street);
        $this->assertSame("86", $entity->street_number);
        $this->assertSame("10559", $entity->postcode);
        $this->assertSame("ΑΘΗΝΑ", $entity->city);
    }
    
    public function test_invalid_vat_number()
    {
        $vies = new VIES();
        $entity = $vies->handle('EL', '000000000');

        $this->assertNull($entity);
    }
}