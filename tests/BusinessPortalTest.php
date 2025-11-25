<?php

namespace Tests;

use Firebed\VatRegistry\BusinessPortal\BusinessPortal;
use Firebed\VatRegistry\BusinessPortal\BusinessPortalException;
use Firebed\VatRegistry\BusinessPortal\InvalidTinException;
use PHPUnit\Framework\TestCase;

class BusinessPortalTest extends TestCase
{
    /**
     * @throws BusinessPortalException
     * @throws InvalidTinException
     */
    public function test_valid_vat_number()
    {
        $env = new Env();

        $portal = new BusinessPortal($env->get('BUSINESS_PORTAL_TOKEN'));
        $company = $portal->searchCompany('094014201');

        $this->assertSame("237901000", $company->getRegistrationNumber());
        $this->assertSame("094014201", $company->getTin());
    }
}