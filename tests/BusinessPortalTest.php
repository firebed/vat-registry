<?php

namespace Tests;

use Firebed\VatRegistry\BusinessPortal;
use PHPUnit\Framework\TestCase;

class BusinessPortalTest extends TestCase
{
    public function test_valid_vat_number()
    {
        $portal = new BusinessPortal();
        $response = $portal->handle('094014201');

        $this->assertSame("000237954001", $response->company->id);
        $this->assertSame("094014201", $response->company->afm);
    }
}