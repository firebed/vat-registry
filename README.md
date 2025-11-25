# Search for Basic Business Registry Information

Using this service, legal entities, legal persons, and natural persons with income from business activity can search for basic information in order to verify the tax or professional status of other legal entities, legal persons, or taxpayers/natural persons conducting business activity.

The system provides 3 ways to search for basic business registry information:

- Through the Basic Business Registry Information Search Service
- Through the Business Registry Service (ΓΕ.ΜΗ.)
- Through the VAT Information Exchange System (VIES) Service

## Installation

```bash
composer require firebed/vat-registry
```

## Through the Basic Business Registry Information Search Service

This service allows searching all Greek VAT numbers. For the search, you will need a `username` and a `password`.

Registration process:

- Register with the [service](https://www1.aade.gr/webtax/wspublicreg/faces/pages/wspublicreg/menu.xhtml) using TAXISnet credentials.
- Obtain special access credentials through the [Special Credentials Management](https://www1.aade.gr/sgsisapps/tokenservices/protected/displayConsole.htm) application.

For more details and registration, visit the [Official AADE Page](https://www.aade.gr/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon).

After registration, you will have the `username` and `password` needed to use the service.

```php
use Firebed\VatRegistry\TaxisNet;
use Firebed\VatRegistry\VatException;

$username = 'your-username';
$password = 'your-password';

$taxis = new TaxisNet($username, $password);

try {
    $entity = $taxis->handle('094014201');
    
    print_r($entity);
} catch (VatException $exception) {
    echo "Σφάλμα: " . $exception->getMessage();
}
```

The result of the above call:

```php
Firebed\VatRegistry\VatEntity {
  +vatNumber: "094014201"
  +taxAuthorityId: "1159"
  +taxAuthorityName: "ΦΑΕ ΑΘΗΝΩΝ"
  +flagDescription: "ΜΗ ΦΠ"
  +valid: true
  +validityDescription: "ΕΝΕΡΓΟΣ ΑΦΜ"
  +firmFlagDescription: "ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ"
  +legalName: "ΤΡΑΠΕΖΑ ΕΘΝΙΚΗ ΤΗΣ ΕΛΛΑΔΟΣ ΑΝΩΝΥΜΗ ΕΤΑΙΡΕΙΑ"
  +commerceTitle: ""
  +legalStatusDescription: "ΑΕ"
  +street: "ΑΙΟΛΟΥ"
  +streetNumber: "86"
  +postcode: "10559"
  +city: "ΑΘΗΝΑ"
  +registrationDate: "1900-01-01"
  +stopDate: ""
  +normalVat: true
  +firms: array:2 [
    0 => array:4 [
      "code" => "64191204"
      "description" => "ΥΠΗΡΕΣΙΕΣ ΤΡΑΠΕΖΩΝ"
      "kind" => "1"
      "kindDescription" => "ΚΥΡΙΑ"
    ]
    1 => array:4 [
      "code" => "66221001"
      "description" => "ΥΠΗΡΕΣΙΕΣ ΑΣΦΑΛΙΣΤΙΚΟΥ ΠΡΑΚΤΟΡΑ ΚΑΙ ΑΣΦΑΛΙΣΤΙΚΟΥ ΣΥΜΒΟΥΛΟΥ"
      "kind" => "2"
      "kindDescription" => "ΔΕΥΤΕΡΕΥΟΥΣΑ"
    ]
  ]
}
```

### Check Natural Person / Company

```php
$entity->isNaturalPerson();
$entity->isCompany();
```

### Check Activity Status

```php
// Returns true if the business is active
// Returns false if the business has been discontinued
$entity->isActive();
```

If the VAT number is not valid, a `null` value is returned. If there was another issue, the `VatException` will contain the relevant error message.

## Through the Business Registry Service (ΓΕ.ΜΗ.)

This service allows searching all VAT numbers registered in the General Commercial Registry (ΓΕ.ΜΗ.).
The Business Portal API requires an API key. You can request an API key by registering at [https://opendata.businessportal.gr/register/](https://opendata.businessportal.gr/register/) and following the instructions to obtain an API key for the Open Data API.

- Official Swagger documentation: [https://opendata-api.businessportal.gr/opendata/docs/](https://opendata-api.businessportal.gr/opendata/docs/)
- Technical details about the API: [https://opendata.businessportal.gr/techdocs/](https://opendata.businessportal.gr/techdocs/)

Before processing the request to the Business Portal API, this package will first check if the provided VAT number is valid, according to the Greek VAT number format. In case the VAT number is invalid, an `InvalidTinException` will be thrown.

```php

use Firebed\VatRegistry\BusinessPortal\BusinessPortal;

$portal = new BusinessPortal('your-api-key');

// Search by company TIN
$response = $portal->searchCompany('094014201');

// Search by company registration number (ΓΕ.Ε.Μ.)
$response = $portal->showCompany('000237954001');

var_dump($response);
```


## Through the VAT Information Exchange System (VIES) Service

Using the VIES Service, you can verify the validity of a VAT number issued by any member state of the European Union. The details it provides are more limited compared to the AADE service.

The service is provided free of charge without registration with any entity. It accepts 2 parameters:
- The country code (e.g., EL for Greece)
- The VAT number you want to verify.

```php
use Firebed\VatRegistry\VIES;
use Firebed\VatRegistry\VatException;

$taxis = new VIES();

try {
    $entity = $taxis->handle('EL', '094014201');
    
    print_r($entity);
} catch (VatException $exception) {
    echo "Σφάλμα: " . $exception->getMessage();
}
```

The result of the above call:

```php
Firebed\VatRegistry\VatEntity {
  +vatNumber: "094014201"
  +taxAuthorityId: null
  +taxAuthorityName: null
  +flagDescription: null
  +valid: true
  +validityDescription: null
  +firmflagDescription: null
  +legalName: "ΤΡΑΠΕΖΑ ΕΘΝΙΚΗ ΤΗΣ ΕΛΛΑΔΟΣ ΑΝΩΝΥΜΗ ΕΤΑΙΡΕΙΑ"
  +commerceTitle: null
  +legalStatusDescription: null
  +street: "ΑΙΟΛΟΥ"
  +streetNumber: "86"
  +postcode: "10559"
  +city: "ΑΘΗΝΑ"
  +registrationDate: null
  +stopDate: null
  +normalVat: null
  +firms: []
}
```

If the VAT number is not valid, the service returns `null`.