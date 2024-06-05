<?php

namespace Firebed\VatRegistry;

use SoapClient;
use Throwable;

class VIES
{
    use Helpers;

    private const ENDPOINT = "https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";

    /**
     * @throws VatException
     */
    public function handle(string $countryCode, string $vatNumber): ?VatEntity
    {
        try {
            $response = $this->request($countryCode, $vatNumber);
            return $this->handleResponse($response);
        } catch (Throwable $e) {
            throw new VatException($e->getMessage());
        }
    }

    /**
     * @throws VatException
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function request(string $countryCode, string $vatNumber)
    {
        $client = $this->createSoapClient();
        $response = $client->checkVat(compact('countryCode', 'vatNumber'));

        if (!$response->valid) {
            return null;
        }

        return $response;
    }

    protected function createSoapClient(): SoapClient
    {
        return new SoapClient(self::ENDPOINT);
    }

    protected function handleResponse($response): ?VatEntity
    {
        if (!$response) {
            return null;
        }

        $vat = new VatEntity();
        $vat->vatNumber = $response->vatNumber;
        $vat->valid = $response->valid;
        $vat->legalName = $response->name;

        $address = $this->trim($this->beforeLast($response->address, ' - '));
        $street = $this->trim($this->beforeLast($address, ' '));

        $vat->postcode = $this->trim($this->afterLast($address, ' '));
        $vat->street = $this->trim($this->beforeLast($street, ' '));
        $vat->streetNumber = $this->trim($this->afterLast($street, ' '));
        $vat->city = $this->trim($this->afterLast($response->address, ' - '));
        return $vat;
    }
}