<?php

namespace Tests;

use Firebed\VatRegistry\TaxisNet;
use Firebed\VatRegistry\VIES;
use PHPUnit\Framework\TestCase;

class TaxisTest extends TestCase
{
    private function credentials(): array
    {
        $variables = [];

        $filePath = __DIR__.'/../.env';
        if (!file_exists($filePath)) {
            echo "File not found: $filePath".PHP_EOL;
            return $variables;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse the line into a key and value
            list($name, $value) = explode('=', $line, 2);

            // Trim whitespace
            $name = trim($name ?? '');
            $value = trim($value ?? '');

            // Remove surrounding quotes from the value if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            $variables[$name] = $value;
        }

        return [$variables['GGPS_USERNAME'], $variables['GGPS_PASSWORD']];
    }

    public function test_valid_vat_number()
    {
        [$username, $password] = $this->credentials();

        $registry = new TaxisNet($username, $password);
        $entity = $registry->handle('094014201');

        $this->assertTrue($entity->valid);
        $this->assertSame("094014201", $entity->vatNumber);
        $this->assertSame("1159", $entity->tax_authority_id);
        $this->assertSame("ΦΑΕ ΑΘΗΝΩΝ", $entity->tax_authority_name);
        $this->assertSame("ΜΗ ΦΠ", $entity->flag_description);
        $this->assertSame("ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ", $entity->firm_flag_description);
        $this->assertSame("ΕΝΕΡΓΟΣ ΑΦΜ", $entity->validity_description);
        $this->assertSame("ΤΡΑΠΕΖΑ ΕΘΝΙΚΗ ΤΗΣ ΕΛΛΑΔΟΣ ΑΝΩΝΥΜΗ ΕΤΑΙΡΕΙΑ", $entity->legalName);
        $this->assertSame("ΑΕ", $entity->legal_status_description);
        $this->assertSame("ΑΙΟΛΟΥ", $entity->street);
        $this->assertSame("86", $entity->street_number);
        $this->assertSame("10559", $entity->postcode);
        $this->assertSame("ΑΘΗΝΑ", $entity->city);
        $this->assertSame("1900-01-01", $entity->registration_date);
        $this->assertNull($entity->stop_date);
        $this->assertSame(true, $entity->normal_vat);

        $this->assertCount(2, $entity->firms);
        
        $this->assertSame("64191204", $entity->firms[0]['code']);
        $this->assertSame("ΥΠΗΡΕΣΙΕΣ ΤΡΑΠΕΖΩΝ", $entity->firms[0]['description']);
        $this->assertSame("1", $entity->firms[0]['kind']);
        $this->assertSame("ΚΥΡΙΑ", $entity->firms[0]['kind_description']);

        $this->assertSame("66221001", $entity->firms[1]['code']);
        $this->assertSame("ΥΠΗΡΕΣΙΕΣ ΑΣΦΑΛΙΣΤΙΚΟΥ ΠΡΑΚΤΟΡΑ ΚΑΙ ΑΣΦΑΛΙΣΤΙΚΟΥ ΣΥΜΒΟΥΛΟΥ", $entity->firms[1]['description']);
        $this->assertSame("2", $entity->firms[1]['kind']);
        $this->assertSame("ΔΕΥΤΕΡΕΥΟΥΣΑ", $entity->firms[1]['kind_description']);
    }

    public function test_invalid_vat_number()
    {
        [$username, $password] = $this->credentials();

        $registry = new TaxisNet($username, $password);
        $entity = $registry->handle('000000000');

        $this->assertNull($entity);
    }
}