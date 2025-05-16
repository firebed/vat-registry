<?php

namespace Firebed\VatRegistry;

class BusinessPortal
{
    use Helpers;

    private const AUTOCOMPLETE = "https://publicity.businessportal.gr/api/autocomplete/";
    private const ENDPOINT     = "https://publicity.businessportal.gr/api/company/details";

    public function handle(string $vatNumber): ?object
    {
        $registrationNumber = $this->getRegistryNumber($vatNumber);

        if (!$registrationNumber) {
            return null;
        }

        return $this->searchByRegistrationNumber($registrationNumber);
    }

    private function searchByRegistrationNumber(string $registryNumber): ?object
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::ENDPOINT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "query" => [
                "arGEMI" => $registryNumber
            ],
            "token" => null,
            "language" => "el"
        ]));

        $headers = [];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:'.curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($result, true);
        if (empty($response['companyInfo']['payload'])) {
            return null;
        }

        return json_decode(json_encode($response['companyInfo']['payload']));
    }

    private function getRegistryNumber(string $vatNumber): ?string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::AUTOCOMPLETE.$vatNumber);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "token" => null,
            "language" => "el"
        ]));

        $headers = [];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:'.curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($result, true);

        foreach ($response['payload']['autocomplete'] as $entry) {
            if ($entry['afm'] === $vatNumber) {
                return $entry['arGemi'];
            }
        }

        return null;
    }
}