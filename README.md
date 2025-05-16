# Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων

Με τη χρήση αυτής της υπηρεσίας, τα νομικά πρόσωπα, οι νομικές οντότητες,
και τα φυσικά πρόσωπα με εισόδημα από επιχειρηματική δραστηριότητα μπορούν
να αναζητήσουν βασικές πληροφορίες, προκειμένου να διακριβώσουν τη φορολογική
ή την επαγγελματική υπόσταση άλλων νομικών προσώπων ή νομικών οντοτήτων ή
φορολογουμένων/φυσικών προσώπων που ασκούν επιχειρηματική δραστηριότητα.

Το σύστημα παρέχει 2 τρόπους αναζήτησης βασικών στοιχείων μητρώου επιχειρήσεων:

- Μέσω της Υπηρεσίας Αναζήτησης Βασικών Στοιχείων Μητρώου Επιχειρήσεων
- Μέσω της Υπηρεσίας ΓΕ.ΜΗ. (Business Registry)
- Μέσω της Υπηρεσίας Vat Information Exchange System (VIES)

## Installation

```bash
composer require firebed/vat-registry
```

## Μέσω της Υπηρεσίας Αναζήτησης Βασικών Στοιχείων Μητρώου Επιχειρήσεων

Η υπηρεσία αυτή επιτρέπει την αναζήτηση όλων των Ελληνικών ΑΦΜ. Για την αναζήτηση
θα χρειαστείτε ένα `username` και ένα `password`.

Διαδικασία εγγραφής:

- Εγγραφή στην [υπηρεσία](https://www1.aade.gr/webtax/wspublicreg/faces/pages/wspublicreg/menu.xhtml) κάνοντας χρήση των κωδικών TAXISnet.
- Απόκτηση ειδικών κωδικών πρόσβασης μέσω της εφαρμογής [Διαχείριση Ειδικών Κωδικών](https://www1.aade.gr/sgsisapps/tokenservices/protected/displayConsole.htm).

Για περισσότερες λεπτομέρειες και για την εγγραφή επισκεφτείτε
την [Επίσημη Σελίδα της ΑΑΔΕ](https://www.aade.gr/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon).

Μετά την εγγραφή, θα έχετε τα `username` και `password` που θα χρειαστείτε για την
χρήση της υπηρεσίας.

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

Το αποτέλεσμα της παραπάνω κλήσης:

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

Σε περίπτωση που το ΑΦΜ δεν είναι έγκυρο επιστρέφεται τιμή `null`. Αν υπήρξε κάποιο άλλο πρόβλημα το `VatException` θα
περιέχει το σχετικό μήνυμα σφάλματος.

## Μέσω της Υπηρεσίας ΓΕ.ΜΗ. (Business Registry)

Η υπηρεσία αυτή επιτρέπει την αναζήτηση όλων των ΑΦΜ που είναι καταχωρημένα
στο Γενικό Εμπορικό Μητρώο (ΓΕ.ΜΗ.). Δεν απαιτείται εγγραφή στην υπηρεσία.

[https://businessregistry.gr/](https://businessregistry.gr/)

> [!CAUTION]
> This is an initial draft, and the API may change in the future.

```php

use Firebed\VatRegistry\BusinessPortal;

$portal = new BusinessPortal();
$response = $portal->handle('094014201');

var_dump($response);
```

## Μέσω της Υπηρεσίας Vat Information Exchange System (VIES)

Με τη χρήση της Υπηρεσία VIES μπορείτε να επαληθεύσετε την εγκυρότητα του ΑΦΜ,
που χορηγείται απο οποιοδήποτε κράτος μέλος της Ευρωπαϊκής Ένωσης. Οι λεπτομέρειες
που παρέχει είναι πιο περιορισμένες σε σχέση με την υπηρεσία της ΑΑΔΕ.

Η Υπηρεσία παρέχεται δωρεάν χωρίς εγγραφή σε κάποιο φορέα. Δέχεται 2 παραμέτρους:
- Τον κωδικό της χώρας (π.χ. EL για Ελλάδα)
- Τον ΑΦΜ που θέλετε να επαληθεύσετε.

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

Το αποτέλεσμα της παραπάνω κλήσης:

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

Σε περίπτωση που το ΑΦΜ δεν είναι έγκυρο, η υπηρεσία επιστρέφει `null`.

## Helper methods

### Έλεγχος Φυσικού Προσώπου / Εταιρείας

```php
$entity->isNaturalPerson();
$entity->isCompany();
```

### Έλεγχος διατήρησης δραστηριότητας

```php
// Επιστρέφει true αν η επιχείρηση είναι ενεργή
// Επιστρέφει false αν η επιχείρηση έχει διακοπεί
$entity->isActive();
```